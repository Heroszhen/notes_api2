<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google\Client as GoogleClient;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GoogleLoginController extends AbstractController
{
    #[Route('/api/login/google', name: 'api_login_google', methods: ['POST'])]
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        JWTTokenManagerInterface $jwtManager,
        LoggerInterface $logger,
        #[Autowire('%env(OAUTH_GOOGLE_CLIENT_ID)%')] string $googleClientId,
    ): JsonResponse {
        $credential = $request->toArray()['credential'] ?? null;
        if (!\is_string($credential)) {
            return $this->json(['message' => 'Token Google is required.'], Response::HTTP_BAD_REQUEST);
        }

        $payload = false;
        try {
            $payload = (new GoogleClient(['client_id' => $googleClientId]))->verifyIdToken($credential);
        } catch (\Exception $e) {
            $logger->error('GoogleLoginController', [$e->getMessage()]);
        }

        if (!$payload || !($payload['email_verified'] ?? false)) {
            return $this->json(['message' => 'Invalid Token Google'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $payload['email']]);

        if (!$user) {
            return $this->json(['message' => 'Account error'], Response::HTTP_BAD_REQUEST);
        }

        if (null === $user->getGoogleId()) {
            $user->setGoogleId($payload['sub']);
            $em->flush();
        }

        return $this->json(['token' => $jwtManager->create($user)]);
    }
}
