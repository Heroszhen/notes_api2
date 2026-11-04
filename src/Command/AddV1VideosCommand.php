<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Actress;
use App\Entity\Video;
use App\Enum\VideoTypeEnum;
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
 * @exemple php bin/console app:add-v1-videos --filename=video.csv
 */
#[AsCommand(
    name: 'app:add-v1-videos',
    description: 'Add a short description for your command',
)]
class AddV1VideosCommand
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
        $io->title('app:add-v1-videos');

        if (empty($filename)) {
            $io->error(sprintf('filename is not found'));

            return Command::FAILURE;
        }

        $path = $this->projectDir.$this->folderPath.$filename;
        if (!$this->filesystem->exists($path)) {
            $io->error(sprintf('%s not found in %s', $filename, $this->folderPath));

            return Command::FAILURE;
        }

        $actress = $this->actressRepository->findOneBy(['name' => 'Générale']);
        if (!$actress instanceof Actress) {
            $io->error(sprintf('Actress %s not found', 'Générale'));

            return Command::FAILURE;
        }

        $tab = $this->utilService->readCSV($path, '{');
        $io->progressStart(count($tab));
        foreach ($tab as $videoV1) {
            $description = $videoV1['actressname'];
            $description .= 'NULL' === $videoV1['description'] ? '' : $videoV1['description'];

            $video = (new Video())
                ->setDescription($description)
                ->setActress($actress)
                ->setVideoUrl($videoV1['videourl']);

            $videoType = VideoTypeEnum::EXTERN;
            switch ($videoV1['videotype']) {
                case '1':
                    $videoType = VideoTypeEnum::INTEGRATION;
                    break;
                case '2':
                    $videoType = VideoTypeEnum::DOUYIN;

                    if (str_contains($videoV1['videourl'], '.mp4')) {
                        $videoType = VideoTypeEnum::MP4;
                    }
                    break;
                case '3':
                    break;
                case '4':
                    $videoType = VideoTypeEnum::TIKTOK;
                    break;
                case '5':
                    $videoType = VideoTypeEnum::YOUTUBE_SHORT;
                    break;
                case '6':
                    $videoType = VideoTypeEnum::INSTAGRAM;
                    break;
                case '7':
                    $videoType = VideoTypeEnum::DAILYMOTION;
                    break;
            }

            if (VideoTypeEnum::INTEGRATION !== $videoType && VideoTypeEnum::MP4 !== $videoType) {
                $video->setVideoUrl($videoV1['siteurl']);
            }

            $video
                ->setTitle($videoV1['name'])
                ->setVideoType($videoType)
                ->setVisible('0' === $videoV1['hidden'] ? true : false)
                ->setWebsite($videoV1['siteurl'])
                ->setCreatedAt(new \DateTime($videoV1['created']));

            $this->entityManager->persist($video);

            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();

        $io->success('Done');

        return Command::SUCCESS;
    }
}
