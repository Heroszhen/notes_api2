<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/documents')]
#[IsGranted('ROLE_ADMIN')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly PaginatorInterface $paginator,
        private readonly EntityManagerInterface $entityManager,
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

    #[Route('/edit/{id}', name: 'app_admin_edit_document', defaults: ['id' => null], requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function editDocument(Request $request, ?string $id): Response
    {
        $isCreated = true;
        if (null === $id) {
            $doc = new Document();
        } else {
            $doc = $this->documentRepository->find($id);

            if (!$doc instanceof Document) {
                return $this->redirectToRoute('app_admin_documents');
            }

            $isCreated = false;
        }

        $form = $this->createForm(DocumentType::class, $doc);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($doc);
            $this->entityManager->flush();

            $this->addFlash('success', 'Enregistré avec succès');

            if (true === $isCreated) {
                return $this->redirectToRoute('app_admin_documents');
            }
        }

        return $this->render('admin/document/edit.html.twig', [
            'form' => $form,
            'doc' => $doc,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_document', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function deleteDoc(Request $request, string $id): Response
    {
        $doc = $this->documentRepository->find($id);

        if ($doc instanceof Document) {
            $this->entityManager->remove($doc);
            $this->entityManager->flush();

            $this->addFlash('success', 'Document supprimé avec succès');
        } else {
            $this->addFlash('danger', 'Document non trouvé');
        }

        return $this->redirectToRoute('app_admin_documents');
    }
}
