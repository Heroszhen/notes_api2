<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractMainController;
use App\Entity\MediaObject;
use App\Entity\Video;
use App\Form\VideoType as VideoFormType;
use App\Repository\VideoRepository;
use App\Service\S3Service;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/videos')]
#[IsGranted('ROLE_ADMIN')]
final class VideoController extends AbstractMainController
{
    public function __construct(
        private readonly VideoRepository $videoRepository,
        private readonly PaginatorInterface $paginator,
        private readonly EntityManagerInterface $entityManager,
        private readonly S3Service $s3Service,
        #[Autowire(env: 'S3_ROOT')] private readonly string $s3Root,
        #[Autowire(env: 'S3_ROOT_AUDIO')] private readonly string $s3RootAudio,
    ) {
    }

    #[Route('', name: 'app_admin_videos')]
    public function index(Request $request): Response
    {
        $qb = $this->videoRepository->createQueryBuilder('video')->orderBy('video.createdAt', 'DESC');

        $videos = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            20
        );

        $request->getSession()->set('app_admin_videos_url', $request->getUri());

        return $this->render('admin/video/index.html.twig', [
            'videos' => $videos,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_edit_video', defaults: ['id' => null], requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function editVideo(Request $request, ?string $id): Response
    {
        $isCreated = true;
        if (null === $id) {
            $video = new Video();
        } else {
            $video = $this->videoRepository->find($id);

            if (!$video instanceof Video) {
                return $this->getRedirectionUrl($request, 'app_admin_videos');
            }

            $isCreated = false;
        }

        $oldVideoUrl = $video->getVideoUrl();

        $form = $this->createForm(VideoFormType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // add photo
            $photoFile = $form->get('photoFile')->getData();
            $oldPhoto = null;
            if ($photoFile instanceof UploadedFile) {
                $photoMediaObject = $this->s3Service->sendFile($photoFile, $this->s3Root);

                if ($photoMediaObject instanceof MediaObject) {
                    $oldPhoto = $video->getPhoto();
                    $video->setPhoto($photoMediaObject);
                }
            }

            // add video mp4
            $toDeleteOldVideo = false;
            $videoFile = $form->get('videoFile')->getData();
            if ($videoFile instanceof UploadedFile) {
                $videoName = $this->s3Service->sendFile($videoFile, $this->s3RootAudio, false);

                if (is_string($videoName)) {
                    $video->setVideoUrl($videoName);
                    $toDeleteOldVideo = true;
                }
            }

            $this->entityManager->persist($video);
            $this->entityManager->flush();

            // delete old photo
            if ($oldPhoto instanceof MediaObject) {
                $this->entityManager->initializeObject($oldPhoto);

                $this->entityManager->remove($oldPhoto);
                $this->entityManager->flush();
            }

            // delete old video mp4
            if (is_string($oldVideoUrl) && !empty($oldVideoUrl) && true === $toDeleteOldVideo) {
                $this->s3Service->deleteFile($oldVideoUrl, $this->s3RootAudio);
            }

            $this->addFlash('success', 'Enregistrée avec succès');

            if (true === $isCreated) {
                return $this->getRedirectionUrl($request, 'app_admin_videos');
            }

            $form = $this->createForm(VideoFormType::class, $video);
        }

        return $this->render('admin/video/edit.html.twig', [
            'form' => $form,
            'video' => $video,
        ]);
    }
}
