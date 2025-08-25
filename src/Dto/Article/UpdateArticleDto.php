<?php

namespace App\Dto\Article;

use App\Dto\Interfaces\ArticleRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateArticleDto implements ArticleRequestInterface
{
    public function __construct(
        #[Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $title = null,

        #[Assert\Length(
            min: 10,
            minMessage: 'Le contenu doit contenir au moins {{ limit }} caractères',
        )]
        private readonly ?string $content = null,

        #[Assert\Length(
            min: 5,
            max: 255,
            minMessage: 'Le contenu court doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le contenu court ne peut pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $shortContent = null,

        private readonly bool $enabled = false,

        #[Assert\Positive(message: 'L\'utilisateur doit être un identifiant valide')]
        private readonly ?int $user = null,
    ) {
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getShortContent(): ?string
    {
        return $this->shortContent;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }
}