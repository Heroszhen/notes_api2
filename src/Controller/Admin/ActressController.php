<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Actress;
use App\Entity\MediaObject;
use App\Form\ActressType;
use App\Repository\ActressRepository;
use App\Service\S3Service;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/actresses')]
#[IsGranted('ROLE_ADMIN')]
final class ActressController extends AbstractController
{
    public function __construct(
        private readonly ActressRepository $actressRepository,
        private readonly PaginatorInterface $paginator,
        private readonly EntityManagerInterface $entityManager,
        private readonly S3Service $s3Service,
    ) {
    }

    #[Route('/', name: 'app_admin_actresses')]
    public function index(Request $request): Response
    {
        $qb = $this->actressRepository->createQueryBuilder('actress')->orderBy('actress.name', 'ASC');

        $actresses = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/actress/index.html.twig', [
            'actresses' => $actresses,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_edit_actress', defaults: ['id' => null], requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function editActress(Request $request, ?string $id): Response
    {
        $isCreated = true;
        if (null === $id) {
            $actress = new Actress();
        } else {
            $actress = $this->actressRepository->find($id);

            if (!$actress instanceof Actress) {
                return $this->redirectToRoute('app_admin_actresses');
            }

            $isCreated = false;
        }

        $form = $this->createForm(ActressType::class, $actress);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPhoto = null;
            $file = $form->get('photo')->getData();
            if ($file instanceof UploadedFile) {
                $mediaObject = $this->s3Service->sendFile($file);

                if ($mediaObject instanceof MediaObject) {
                    $oldPhoto = $actress->getPhoto();
                    $actress->setPhoto($mediaObject);
                }
            }

            $this->entityManager->persist($actress);
            $this->entityManager->flush();

            if ($oldPhoto instanceof MediaObject) {
                $this->entityManager->initializeObject($oldPhoto);

                $this->entityManager->remove($oldPhoto);
                $this->entityManager->flush();
            }

            $this->addFlash('success', 'Enregistrée avec succès');

            if (true === $isCreated) {
                return $this->redirectToRoute('app_admin_actresses');
            }
        }

        return $this->render('admin/actress/edit.html.twig', [
            'form' => $form,
            'actress' => $actress,
        ]);
    }
}
