<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractMainController;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
final class CategoryController extends AbstractMainController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/{id}', name: 'app_admin_categories', defaults: ['id' => null], requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function index(Request $request, ?string $id): Response
    {
        if (null === $id) {
            $category = new Category();
        } else {
            $category = $this->categoryRepository->find($id);

            if (!$category instanceof Category) {
                return $this->redirectToRoute('app_admin_actresses');
            }
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'Enregistrée avec succès');

            if (null === $id) {
                $category = new Category();
                $form = $this->createForm(CategoryType::class, $category);
            }
        }

        $categories = $this->categoryRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/category/index.html.twig', [
            'form' => $form,
            'category' => $category,
            'categories' => $categories,
        ]);
    }
}
