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
use Psr\Log\LoggerInterface;
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
        private readonly LoggerInterface $log,
        #[Autowire(env: 'S3_ROOT')] private readonly string $s3Root,
        #[Autowire(env: 'S3_ROOT_AUDIO')] private readonly string $s3RootAudio,
        #[Autowire(env: 'S3_URL')] private readonly string $s3Url,
        #[Autowire(env: 'S3_BUCKET')] private readonly string $s3Bucket,
    ) {
    }

    #[Route('', name: 'app_admin_videos')]
    public function index(Request $request): Response
    {
        $videos = [];

        try {
            $qb = $this->videoRepository->createQueryBuilder('video')->orderBy('video.createdAt', 'DESC');

            $videos = $this->paginator->paginate(
                $qb,
                $request->query->getInt('page', 1),
                20
            );

            $request->getSession()->set('app_admin_videos_url', $request->getUri());
        } catch (\Throwable $e) {
            $this->log->error('app_admin_videos', [$e->getMessage()]);
            $this->addFlash('danger', 'Impossible de récupérer les vidéos');
        }

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
            try {
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
                        $video->setVideoUrl("{$this->s3Url}/{$this->s3RootAudio}/{$videoName}");
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
                    if (str_contains($oldVideoUrl, $this->s3Bucket)) {
                        $tab = explode('/', $oldVideoUrl);
                        $this->s3Service->deleteFile(end($tab), $this->s3RootAudio);
                    }
                }

                $this->addFlash('success', 'Enregistrée avec succès');

                if (true === $isCreated) {
                    return $this->getRedirectionUrl($request, 'app_admin_videos');
                }

                $form = $this->createForm(VideoFormType::class, $video);
            } catch (\Throwable $e) {
                $this->log->error('app_admin_edit_video', [$e->getMessage()]);
                $this->addFlash('danger', "Erreur sur l'enregistrement de la vidéo");
            }
        }

        $previousVideo = null;
        $nextVideo = null;
        if (null !== $id) {
            $nextVideo = $this->videoRepository->findNextVideo($video);
            $previousVideo = $this->videoRepository->findPreviousVideo($video);
        }

        return $this->render('admin/video/edit.html.twig', [
            'form' => $form,
            'video' => $video,
            'previousVideo' => $previousVideo,
            'nextVideo' => $nextVideo,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_video', requirements: ['id' => Requirement::UUID], methods: ['DELETE'])]
    public function deleteVideo(Request $request, string $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        if (!$this->isCsrfTokenValid('delete' . $id, $request->query->getString('_token'))) {
            return $this->json(null, Response::HTTP_FORBIDDEN);
        }

        $video = $this->videoRepository->find($id);
        if (!$video instanceof Video) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        if (null !== $video->getPhoto()) {
            $this->entityManager->initializeObject($video->getPhoto());
        }

        $this->entityManager->remove($video);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
