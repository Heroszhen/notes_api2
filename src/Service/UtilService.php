<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class UtilService
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function logHttpErrorMessage(\Exception $e, string $title = 's3'): void
    {
        $this->logger->error("{$title}: {$e->getMessage()}");
        throw new BadRequestHttpException($e->getMessage());
    }
}
