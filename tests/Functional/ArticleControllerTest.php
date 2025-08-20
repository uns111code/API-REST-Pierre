<?php

namespace App\Tests\Functional;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ArticleControllerTest extends WebTestCase
{
    // Propriété qui va stocker notre client léger (pour envoyer des requêtes)
    private KernelBrowser $client;
    private AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        // Création du client léger pour les tests
        $this->client = self::createClient(server: [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json'
        ]);

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    private function getUser(string $username = 'admin'): ?User
    {
        // On load les fixtures
        $this->databaseTool->loadAliceFixture([
            __DIR__ . '/Fixtures/UserFixtures.yaml'
        ]);

        // On récupère l'utilisateur par son nom d'utilisateur
        $user = self::getContainer()->get(UserRepository::class)
            ->findOneBy(['username' => $username]);

        // On le renvois
        return $user;
    }

    public function testIndexEndpointWithNoConnectedUser(): void
    {
        $this->client->request('GET', '/api/admin/articles');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testIndexEndpointWithConnectedUser(): void
    {
        // On connecte d'abord l'utilisateur
        $this->client->loginUser(
            $this->getUser('user'),
            'login'
        );

        $this->client->request('GET', '/api/admin/articles');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testIndexEndpointWithConnectedAdmin(): void
    {
        // On connecte d'abord l'utilisateur
        $this->client->loginUser(
            $this->getUser('admin'),
            'login'
        );

        $this->client->request('GET', '/api/admin/articles');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testIndexEndpointValidateStructureJsonResponse(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        $this->client->request('GET', '/api/admin/articles');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertArrayHasKey('pages', $response['meta']);
        $this->assertArrayHasKey('total', $response['meta']);
    }

    public function testIndexEndpointValidateNumberOfItemsDefault(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        // On charge les fixtures pour le test
        $this->databaseTool->loadAliceFixture([
            __DIR__ . '/Fixtures/ArticleFixtures.yaml'
        ]);

        $this->client->request('GET', '/api/admin/articles');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(6, $response['items']);
    }

    public function testIndexEndpointValidateNumberOfItemsWithLimitParameter(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        $this->databaseTool->loadAliceFixture([
            __DIR__ . '/Fixtures/ArticleFixtures.yaml'
        ]);

        $this->client->request('GET', '/api/admin/articles?limit=1');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(1, $response['items']);
        $this->assertEquals(12, $response['meta']['pages']);
    }

    public function testIndexEndpointValidateErrorWhenLimitIsNotPositive(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        $this->client->request('GET', '/api/admin/articles?limit=-1');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals('limit: This value should be positive.', $response['detail']);
    }

    public function testIndexEndpointValidateFirstItemWhenPageIsChange(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        $this->databaseTool->loadAliceFixture([
            __DIR__ . '/Fixtures/ArticleFixtures.yaml'
        ]);

        $this->client->request('GET', '/api/admin/articles?page=2');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals('Article 7', $response['items'][0]['title']);
    }

    public function testIndexEndpointValidateErrorWhenPageIsNotPositive(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        $this->client->request('GET', '/api/admin/articles?page=-1');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals('page: This value should be positive.', $response['detail']);
    }

    public function testCreateEndpointWithNoConnectedUser(): void
    {
        $this->client->request('POST', '/api/admin/articles');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateEndpointWithConnectedUser(): void
    {
        // On connecte d'abord l'utilisateur
        $this->client->loginUser(
            $this->getUser('user'),
            'login'
        );

        $this->client->request('POST', '/api/admin/articles');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateEndpointWithConnectedAdmin(): void
    {
        $user = $this->getUser();

        // On connecte d'abord l'utilisateur
        $this->client->loginUser(
            $user,
            'login'
        );

        $this->client->request('POST', '/api/admin/articles', [
            'title' => 'Article de test',
            'content' => 'Article de test',
            'shortContent' => 'Article de test',
            'user' => $user->getId(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateEndpointValidateCreationInBdd(): void
    {
        $user = $this->getUser();

        // On connecte d'abord l'utilisateur
        $this->client->loginUser(
            $user,
            'login'
        );

        $this->client->request('POST', '/api/admin/articles', [
            'title' => 'Article de test',
            'content' => 'Article de test',
            'shortContent' => 'Article de test',
            'user' => $user->getId(),
        ]);

        $article = self::getContainer()->get(ArticleRepository::class)->findOneBy(['title' => 'Article de test']);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testUpdateEndpointWithNoConnectedUser(): void
    {
        $this->client->request('PATCH', '/api/admin/articles/1');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateEndpointWithConnectedUser(): void
    {
        // On connecte d'abord l'utilisateur
        $this->client->loginUser(
            $this->getUser('user'),
            'login'
        );

        $this->client->request('PATCH', '/api/admin/articles/1');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateEndpointWithConnectedAdmin(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        $this->databaseTool->loadAliceFixture([
            __DIR__ . '/Fixtures/ArticleFixtures.yaml'
        ]);

        $article = self::getContainer()->get(ArticleRepository::class)->findOneBy(['title' => 'Article 1']);

        $this->client->request('PATCH', "/api/admin/articles/{$article->getId()}", [
            'title' => 'Article modifié',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}