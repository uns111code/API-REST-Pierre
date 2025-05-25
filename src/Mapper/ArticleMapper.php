<?php

namespace App\Mapper;

use App\Entity\Article;
use App\Dto\Interfaces\ArticleRequestInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleMapper
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function map(ArticleRequestInterface $dto, ?Article $article = null): Article
    {
        $article ??= new Article;

        if (null !== $dto->getTitle()) {
            $article->setTitle(
                $dto->getTitle()
            );
        }

        if (null !== $dto->getContent()) {
            $article->setContent(
                $dto->getContent()
            );
        }

        if (null !== $dto->getShortContent()) {
            $article->setShortContent(
                $dto->getShortContent()
            );
        }

        if (null !== $dto->isEnabled()) {
            $article->setEnabled(
                $dto->isEnabled()
            );
        }
        if (null !== $dto->getUser()) {
            $user = $this->userRepository->find($dto->getUser());
            if (null === $user) {
                throw new NotFoundHttpException('Utilisateur introuvable');
            }
            $article->setUser(
                $user
            );
        }

        return $article;
    }

}