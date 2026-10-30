<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/actresse_photos')]
#[IsGranted('ROLE_ADMIN')]
final class ActressPhotoController extends AbstractController
{
    #[Route('', name: 'app_admin_actress_photos')]
    public function index(): Response
    {
        return $this->render('admin/actress_photo/index.html.twig', []);
    }
}
