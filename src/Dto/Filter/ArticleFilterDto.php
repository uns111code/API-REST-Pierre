<?php
namespace App\Dto\Filter;
use symfony\Component\Validator\Constraints as Assert;

class ArticleFilterDto
{
    public function __construct(
        #[Assert\Positive]
        private readonly int $page = 1,
        
        #[Assert\Positive]
        private readonly int $limit = 6,

    ) {
    }
    public function getPage(): int
    {
        return $this->page;
    }
    public function getLimit(): int
    {
        return $this->limit;
    }
}