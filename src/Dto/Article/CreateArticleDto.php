<?php

namespace App\Dto\Article;

use App\Entity\Article;
use App\Dto\Interfaces\ArticleRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(
    fields: ['title'],
    entityClass: Article::class,
    message: 'Cet article existe déjà'
)]
class CreateArticleDto implements ArticleRequestInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'Titre est requis')]
        #[Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Titre doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Titre ne doit pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $title = null,
        #[Assert\NotBlank(message: 'Contenu est requis')]
        #[Assert\Length(
            min: 3,
            minMessage: 'Contenu doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Contenu ne doit pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $content = null,
        #[Assert\Length(
            min: 3,
            max: 255,
            maxMessage: 'Contenu court ne doit pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $shortContent = null,
        #[Assert\NotBlank(message: 'Statut est requis')]

        private readonly bool $enabled = false,
        
        #[Assert\NotBlank(message: 'Utilisateur est requis')]
        #[Assert\Positive(message: 'Utilisateur doit être un entier positif')]
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

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

        /**
         * Get the value of user
         */
        public function getUser(): ?int
        {
                return $this->user;
        }
}