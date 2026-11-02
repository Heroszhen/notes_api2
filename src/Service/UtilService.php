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

    public function readCSV(string $filePath, string $separator = ';'): array
    {
        $rows = [];
        $h = fopen($filePath, 'rb');
        if (false === $h) {
            return $rows;
        }

        $headers = fgetcsv($h, 0, $separator);
        if (false === $headers) {
            fclose($h);

            return $rows;
        }
        $headers = array_map(static fn ($h) => trim((string) $h), $headers);

        while (($data = fgetcsv($h, 0, $separator)) !== false) {
            if (1 === \count($data) && '' === trim((string) $data[0])) {
                continue;
            }
            $row = [];
            foreach ($headers as $i => $col) {
                $row[$col] = isset($data[$i]) ? trim((string) $data[$i]) : null;
            }
            $rows[] = $row;
        }

        fclose($h);

        return $rows;
    }
}
