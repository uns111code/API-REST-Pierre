<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Mapper\ArticleMapper;
use App\Dto\Article\CreateArticleDto;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\Article\UpdateArticleByAdminDto;
use App\Dto\Filter\ArticleFilterDto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[Route('/api/admin/articles', name: 'api_admin_articles_')]
class ArticleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ArticleRepository $articleRepository,
        private readonly ArticleMapper $articleMapper,
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryString()]
        ArticleFilterDto $articleFilterDto
    ): JsonResponse
    {
        // dd($articleFilterDto);
        return $this->json(
            $this->articleRepository->findPaginate($articleFilterDto),
            Response::HTTP_OK,
            ['groups' => ['articles:index', 'article:index', 'article:show']]
        );
    }
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Article $article): JsonResponse
    {
        // $article = $this->articleRepository->find($id);

        // if (!$article) {
        //     return $this->json(['message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        // }

        return $this->json(
            $article,
            Response::HTTP_OK,
            [],
            ['groups' => ['articles:index']]
        );
    }
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Article $article): JsonResponse
    {
        $this->em->remove($article);
        $this->em->flush();

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );
    }


    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload]
        CreateArticleDto $dto): JsonResponse
    {


        $article = $this->articleMapper->map($dto);

        $this->em->persist($article);
        $this->em->flush();

        return $this->json(
            ['id' => $article->getId()],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(
        // parameter converter
        Article $article,
        #[MapRequestPayload]
        UpdateArticleByAdminDto $dto
    ): JsonResponse
    {
        $this->articleMapper->map($dto, $article);
        $this->em->flush();

        return $this->json(
            $article,
            Response::HTTP_OK,
            [],
            ['groups' => ['common:index', 'articles:index', 'articles:show']]
        );
    }
}
