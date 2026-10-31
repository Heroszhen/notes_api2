<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ActressPhoto;
use App\Entity\MediaObject;
use App\Form\ActressPhotoType;
use App\Model\ActressPhotoModel;
use App\Repository\ActressPhotoRepository;
use App\Service\S3Service;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/actresse_photos')]
#[IsGranted('ROLE_ADMIN')]
final class ActressPhotoController extends AbstractController
{
    public function __construct(
        private readonly ActressPhotoRepository $actressPhotoRepository,
        private readonly PaginatorInterface $paginator,
        private readonly EntityManagerInterface $entityManager,
        private readonly S3Service $s3Service,
        #[Autowire(env: 'S3_ROOT')] private readonly string $s3Root,
    ) {
    }

    #[Route('', name: 'app_admin_actress_photos', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $qb = $this->actressPhotoRepository->createQueryBuilder('actress_photo')->orderBy('actress_photo.createdAt', 'DESC');

        $actressePhotos = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('admin/actress_photo/index.html.twig', [
            'actressePhotos' => $actressePhotos,
        ]);
    }

    #[Route('/create', name: 'app_admin_add_actress_photos', methods: ['GET', 'POST'])]
    public function addPhotos(Request $request): Response
    {
        $photoModel = new ActressPhotoModel();
        $form = $this->createForm(ActressPhotoType::class, $photoModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($photoModel->getPhotos() as $file) {
                $mediaObject = $this->s3Service->sendFile($file, $this->s3Root);
                if (!$mediaObject instanceof MediaObject) {
                    continue;
                }

                $actressPhoto = (new ActressPhoto())
                    ->setActress($photoModel->getActress())
                    ->setVisible($photoModel->getVisible())
                    ->setPhoto($mediaObject);

                $this->entityManager->persist($actressPhoto);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Enregistrées avec succès');

            $backUrl = $request->getSession()->get('app_admin_actress_photos_url');

            return \is_string($backUrl)
                ? $this->redirect($backUrl)
                : $this->redirectToRoute('app_admin_actress_photos');
        }

        return $this->render('admin/actress_photo/add.html.twig', [
            'form' => $form,
        ]);
    }
}
