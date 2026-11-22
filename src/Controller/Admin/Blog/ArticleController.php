<?php

namespace App\Controller\Admin\Blog;

use App\Repository\Blog\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/blog/articles')]
#[IsGranted('ROLE_ADMIN')]
final class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $actressRepository,
        private readonly PaginatorInterface $paginator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'app_admin_blog_articles', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $openedForm = false;
        $qb = $this->actressRepository->findBySearchForm();
        /*
        $actressSearchModel = new ActressSearchModel();
        $form = $this->createForm(ActressSearchType::class, $actressSearchModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $openedForm = true;
            $qb = $this->actressRepository->findBySearchForm($actressSearchModel);
        }*/

        $articles = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            20,
            [
                'defaultSortFieldName' => 'article.createdAt',
                'defaultSortDirection' => 'desc',
                'sortFieldAllowList' => ['article.createdAt', 'actress.title', 'actress.birthday'],
            ]
        );

        $request->getSession()->set('app_admin_actresses_url', $request->getUri());

        return $this->render('admin/blog/article/index.html.twig', [
            'articles' => $articles,
            'openedForm' => $openedForm,

        ]);
    }
}
