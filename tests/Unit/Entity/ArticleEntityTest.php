<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Unique;

class ArticleEntityTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        // Init le kernel Symfony
        self::bootKernel();

        // On récupère l'EntityManager qu'on stock dans la propriété
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadFixtures();
    }

    private function getUser(): User
    {
        return (new User)
            ->setEmail('test@example.com')
            ->setPassword('password123  ');
    }

    private function getArticle(): Article
    {
        return (new Article)
            ->setTitle('Titre de Test')
            ->setContent('Contenu de l\'article de test')
            ->setShortContent('Contenu court de l\'article de test')
            ->setUser($this->getUser())
            ->setEnabled(true);
    }

    private function persistData(Article $article, User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->persist($article);

        $this->entityManager->flush();
    }


    public function testGenerationSlugByTitle(): void
    {
        $article = $this->getArticle();

        $this->persistData($article, $article->getUser());

        $this->assertEquals('titre-de-test', $article->getSlug());
    }

    public function testGenerationCreatedAtOnPersist(): void
    {
        $article = $this->getArticle();

        $this->persistData($article, $article->getUser());

        $expected = (new \DateTimeImmutable())->format('Y-m-d H:i');

        $this->assertEquals($expected, $article->getCreatedAt()->format('Y-m-d H:i'));
    }

    public function testGenerationCreatedAtOnPersistWithExistingCreatedAt(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');
        $article = $this->getArticle()
            ->setCreatedAt($createdAt);

        $this->persistData($article, $article->getUser());

        $this->assertEquals(
            $createdAt->format('Y-m-d H:i'),
            $article->getCreatedAt()->format('Y-m-d H:i')
        );
    }

    public function testGenerationUpdatedAtOnUpdated(): void
    {
        $article = $this->getArticle();
        $this->persistData($article, $article->getUser());

        $this->assertNull($article->getUpdatedAt());

        $article
            ->setTitle('Nouveau Titre');
        
        $this->entityManager->flush();

        $expected = (new \DateTimeImmutable())->format('Y-m-d H:i');
        $this->assertEquals($expected, $article->getUpdatedAt()->format('Y-m-d H:i'));
    
    }

    public function testGenerationUpdatedAtOnUpdatedAtAndEnsureUpdatedAtIsChange(): void
    {
        $article = $this->getArticle();
        $this->persistData($article, $article->getUser());

        $updatedAt = new \DateTimeImmutable('2025-08-19 12:00:00');

        $article
            ->setUpdatedAt($updatedAt);
        
        $this->entityManager->flush();

        $this->assertNotEquals($updatedAt, $article->getUpdatedAt());
    
    }

    public function testExceptionNoUniqueTitle(): void
    {
        $this->databaseTool->loadAliceFixture(
            [
                \dirname(__DIR__) . '/Fixtures/ArticleFixtures.yaml',
            ]
        );

        $article = $this->getArticle()
            ->setTitle('Article de test');

        $this->expectException(UniqueConstraintViolationException::class);

        $this->persistData($article, $article->getUser());
    }
}