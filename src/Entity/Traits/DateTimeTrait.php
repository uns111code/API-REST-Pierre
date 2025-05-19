<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

trait DateTimeTrait
{
    #[ORM\Column]
    #[Groups(['common:index'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

        public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist] // PrePersist -> Before the entity is persisted -> avant l'insertion dans la base de données exécute cette fonction
    public function autoSetCreatedAt(): static
    {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTimeImmutable();
        }
        
        return $this;
    }
    #[ORM\PreUpdate] // PreUpdate -> Before the entity is updated -> avant la mise à jour de l'entité exécute cette fonction
    public function autoSetUpdatedAt(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}