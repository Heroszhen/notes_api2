<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Actress;
use App\Entity\ActressPhoto;
use App\Repository\ActressPhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use function Zenstruck\Foundry\faker;

/**
 * @example
 * php bin/console app:add-fake-actresses
 * php bin/console app:add-fake-actresses 100
 */
#[AsCommand(
    name: 'app:add-fake-actresses',
    description: 'Add some fake actresses',
)]
class AddFakeActressesCommand
{
    private const PHOTO_ID = 'd5e02459-1965-4d85-87b3-e143012e986c';
    private const COUNTRY = 'Canada';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ActressPhotoRepository $actressPhotoRepository,
        #[Autowire(env: 'APP_ENV')] private readonly string $appEnv,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Number of actresses to create')] int $count = 60,
    ): int {
        if ('prod' === $this->appEnv) {
            $io->error('This command is not allowed in production');

            return Command::FAILURE;
        }

        $photo = $this->actressPhotoRepository->find(self::PHOTO_ID);
        if (!$photo instanceof ActressPhoto) {
            $io->error('Photo not found');

            return Command::FAILURE;
        }

        $io->progressStart($count);
        for ($i = 0; $i < $count; ++$i) {
            $actress = (new Actress())
                ->setName(faker()->unique()->name('female'))
                ->setCountry(self::COUNTRY)
                ->setBirthday(\DateTimeImmutable::createFromMutable(faker()->dateTimeBetween('-100 years', '-60 years')))
                ->setPhoto($photo->getPhoto())
                ->setCreatedAt(faker()->unique()->dateTimeBetween('-300 years', '-200 years'));

            $this->entityManager->persist($actress);
            $this->entityManager->flush();

            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();

        $io->success('Done');

        return Command::SUCCESS;
    }
}
