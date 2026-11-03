<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Actress;
use App\Entity\ActressPhoto;
use App\Entity\MediaObject;
use App\Repository\ActressRepository;
use App\Service\UtilService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @exemple php bin/console app:add-v1-photos --filename=photos.txt
 */
#[AsCommand(
    name: 'app:add-v1-photos',
    description: 'Add a short description for your command',
)]
class AddV1PhotosCommand
{
    private string $folderPath = '/var/share/';

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly UtilService $utilService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ActressRepository $actressRepository,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option('Name of file to import')] string $filename = '',
    ): int {
        $io->title('app:add-v1-photos');

        if (empty($filename)) {
            $io->error(sprintf('filename is not found'));

            return Command::FAILURE;
        }

        $path = $this->projectDir.$this->folderPath.$filename;
        if (!$this->filesystem->exists($path)) {
            $io->error(sprintf('%s not found in %s', $filename, $this->folderPath));

            return Command::FAILURE;
        }

        $actresses = $this->actressRepository->findAll();
        $actressesByName = [];
        foreach ($actresses as $actress) {
            /** @var Actress $actress */
            if (null === $actress->getName()) {
                continue;
            }

            $actressesByName[trim($actress->getName())] = $actress;
        }

        $tab = $this->utilService->readCSV($path, ',');
        $io->progressStart(count($tab));
        foreach ($tab as $photoV1) {
            $actress = null;
            if ('1' === $photoV1['actress_id']) {
                if (
                    empty($photoV1['name'])
                    || 'Inconnue' === $photoV1['name']
                    || 'NULL' === $photoV1['name']
                ) {
                    $actress = $actressesByName['Générale'];
                } else {
                    $actress = $actressesByName[trim($photoV1['name'])];
                }
            } else {
                $actress = $actressesByName[trim($photoV1['actress_name'])];
            }

            if (null === $actress) {
                $io->warning(sprintf('video %s has no saved actress', $photoV1['id']));
                continue;
            }

            // create photo
            $mediaObject = null;
            if (!empty($photoV1['photoname'])) {
                $mediaObject = (new MediaObject())
                    ->setName($photoV1['photoname'])
                    ->setOriginalName($photoV1['photoname']);

                $this->entityManager->persist($mediaObject);
            } else {
                continue;
            }

            // add photo in created actress_photo
            $photo = (new ActressPhoto())
                ->setActress($actress)
                ->setPhoto($mediaObject)
                ->setCreatedAt(empty($photoV1['created']) ? new \DateTime() : new \DateTime($photoV1['created']));

            $this->entityManager->persist($photo);
            $this->entityManager->flush();

            $io->progressAdvance();
        }

        $io->progressFinish();

        $io->success('Done');

        return Command::SUCCESS;
    }
}
