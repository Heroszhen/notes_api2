<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ActressPhoto;
use App\Entity\MediaObject;
use App\Form\ActressPhotoModifyType;
use App\Form\ActressPhotoType;
use App\Model\ActressPhotoModel;
use App\Repository\ActressPhotoRepository;
use App\Service\S3Service;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
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
            24
        );

        $request->getSession()->set('app_admin_actress_photos_url', $request->getUri());

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

            return $this->getRedirectionUrl($request);
        }

        return $this->render('admin/actress_photo/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_actress_photo', requirements: ['id' => Requirement::UUID], methods: ['DELETE'])]
    public function deletePhoto(Request $request, string $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        if (!$this->isCsrfTokenValid('delete'.$id, $request->query->getString('_token'))) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        $photo = $this->actressPhotoRepository->find($id);
        if (!$photo instanceof ActressPhoto) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->initializeObject($photo->getPhoto());

        $this->entityManager->remove($photo);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/modify/{id}', name: 'app_admin_modify_actress_photo', requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function modifyPhoto(Request $request, ActressPhoto $actressPhoto): Response
    {
        $form = $this->createForm(ActressPhotoModifyType::class, $actressPhoto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($actressPhoto);
            $this->entityManager->flush();

            $this->addFlash('success', 'Enregistrée avec succès');
        }

        return $this->render('admin/actress_photo/modify.html.twig', [
            'form' => $form,
            'actressPhoto' => $actressPhoto,
        ]);
    }

    private function getRedirectionUrl(Request $request): RedirectResponse
    {
        $backUrl = $request->getSession()->get('app_admin_actress_photos_url');

        return \is_string($backUrl)
            ? $this->redirect($backUrl)
            : $this->redirectToRoute('app_admin_actress_photos');
    }

    #[Route('/activate/{id}', name: 'app_admin_activate_actress_photo', requirements: ['id' => Requirement::UUID], methods: ['PATCH'])]
    public function activatePhoto(Request $request, string $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        if (!$this->isCsrfTokenValid('patch'.$id, $request->query->getString('_token'))) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        $photo = $this->actressPhotoRepository->find($id);
        if (!$photo instanceof ActressPhoto) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $visible = $request->getPayload()->get('visible');
        if (!is_bool($visible)) {
            throw new BadRequestHttpException('Visible is not found');
        }

        $photo->setVisible($visible);

        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
