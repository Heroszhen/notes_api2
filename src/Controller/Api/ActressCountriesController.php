<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ActressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/actresses/countries', name: 'api_countries', methods: ['GET'], priority: 1)]
#[IsGranted('ROLE_USER')]
class ActressCountriesController extends AbstractController
{
    public function __invoke(ActressRepository $actressRepository): JsonResponse
    {
        return $this->json($actressRepository->findCountries());
    }
}
