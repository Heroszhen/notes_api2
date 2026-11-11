<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentSearchType;
use App\Repository\DocumentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class HomeController extends AbstractMainController
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $qb = $this->documentRepository->createQueryBuilder('document')->orderBy('document.createdAt', 'DESC');

        $form = $this->createForm(DocumentSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->getData()['keywords'])) {
                $qb = $this->documentRepository->findByKeywords($form->getData()['keywords']);
            }
        }

        $docs = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $request->getSession()->set('app_home_url', $request->getUri());

        return $this->render('home/index.html.twig', [
            'docs' => $docs,
            'form' => $form,
        ]);
    }

    #[Route('/document/{id}', name: 'app_document', requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function readDocument(Document $doc): Response
    {
        return $this->render('home/document.html.twig', [
            'doc' => $doc,
        ]);
    }
}
