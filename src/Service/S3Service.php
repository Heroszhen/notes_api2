<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\MediaObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class S3Service
{
    public function __construct(
        private HttpClientInterface $s3Client,
        private EntityManagerInterface $entityManager,
        private UtilService $utilService,
    ) {
    }

    public function sendFile(UploadedFile $file, bool $toSave = true): ?MediaObject
    {
        $newFileName = uniqid().'_'.$file->getClientOriginalName();
        try {
            $filePart = DataPart::fromPath(
                $file->getPathname(),
                $newFileName,
                $file->getMimeType() ?? 'application/octet-stream'
            );

            $formParts = [
                'file' => $filePart,
                'bucket' => $_ENV['S3_BUCKET'],
                'path' => $_ENV['S3_ROOT'],
            ];

            $formData = new FormDataPart($formParts);
            $headers = $formData->getPreparedHeaders()->toArray();

            $this->s3Client->request('POST', $_ENV['ZHEN_API_ENDPOINT'].'/s3files/file', [
                'headers' => $headers,
                'body' => $formData->bodyToString(),
            ]);

            // dd(json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR));
        } catch (\Exception $e) {
            $this->utilService->logHttpErrorMessage($e);
        }

        if ($toSave) {
            $mediaObject = (new MediaObject())
                ->setOriginalName($file->getClientOriginalName())
                ->setName($newFileName)
                ->setType($file->getClientMimeType())
                ->setSize($file->getSize());

            $this->entityManager->persist($mediaObject);
            $this->entityManager->flush();

            return $mediaObject;
        }

        return null;
    }

    public function deleteFile(string $path): bool
    {
        try {
            $response = $this->s3Client->request('POST', $_ENV['ZHEN_API_ENDPOINT'].'/s3files/delete', [
                'json' => ['bucket' => $_ENV['S3_BUCKET'], 'path' => $_ENV['S3_ROOT'].'/'.$path],
            ]);

            return Response::HTTP_NO_CONTENT === $response->getStatusCode();
        } catch (\Exception $e) {
            $this->utilService->logHttpErrorMessage($e);
        }

        return false;
    }
}
