<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/documents')]
#[IsGranted('ROLE_ADMIN')]
final class DocumentController extends AbstractController
{
    #[Route('/', name: 'app_admin_documents', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/document/index.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }
}
