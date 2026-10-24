<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\DocumentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/documents')]
#[IsGranted('ROLE_ADMIN')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route('/', name: 'app_admin_documents', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $docs = $this->paginator->paginate(
            $this->documentRepository->createQueryBuilder('document')->orderBy('document.createdAt', 'DESC'),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/document/index.html.twig', [
            'docs' => $docs,
        ]);
    }
}
