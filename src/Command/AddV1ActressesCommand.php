<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Actress;
use App\Entity\MediaObject;
use App\Service\UtilService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @exemple php bin/console app:add-v1-actresses --filename=actresses.txt
 */
#[AsCommand(
    name: 'app:add-v1-actresses',
    description: 'Add a short description for your command',
)]
class AddV1ActressesCommand
{
    private string $folderPath = '/var/share/';

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly UtilService $utilService,
        private readonly EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option('Name of file to import')] string $filename = '',
    ): int {
        $io->title('app:add-v1-actresses');

        if (empty($filename)) {
            $io->error(sprintf('filename is not found'));

            return Command::FAILURE;
        }

        $path = $this->projectDir.$this->folderPath.$filename;
        if (!$this->filesystem->exists($path)) {
            $io->error(sprintf('%s not found in %s', $filename, $this->folderPath));

            return Command::FAILURE;
        }

        $tab = $this->utilService->readCSV($path, ',');
        $io->progressStart(count($tab));
        foreach ($tab as $actressV1) {
            // create photo
            $mediaObject = null;
            if (!empty($actressV1['photo'])) {
                $mediaObject = (new MediaObject())
                    ->setName($actressV1['photo'])
                    ->setOriginalName($actressV1['photo']);

                $this->entityManager->persist($mediaObject);
            }

            // add photo in created actress
            $actress = (new Actress())
                ->setName(trim($actressV1['name']))
                ->setBirthday(new \DateTimeImmutable($actressV1['birthday']))
                ->setDescription('NULL' === $actressV1['description'] ? null : $actressV1['description'])
                ->setPhoto($mediaObject)
                ->setCountry($actressV1['country'])
                ->setCreatedAt(empty($actressV1['created']) ? new \DateTime() : new \DateTime($actressV1['created']));

            $this->entityManager->persist($actress);
            $this->entityManager->flush();

            $io->progressAdvance();
        }

        $io->progressFinish();

        $io->success('Done');

        return Command::SUCCESS;
    }
}
