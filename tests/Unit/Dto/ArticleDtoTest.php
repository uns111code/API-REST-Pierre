<?php

namespace App\Tests\Unit\Dto;

use App\Dto\Article\CreateArticleDto;
use App\Dto\Interfaces\ArticleRequestInterface;
use App\Tests\Unit\Traits\ValidationTestTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleDtoTest extends KernelTestCase
{
    use ValidationTestTrait;

    private AbstractDatabaseTool $databaseTool;
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        self::bootKernel();
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);

        $this->databaseTool->loadFixtures();
    }

    private function getArticleDto(array $data = []): ArticleRequestInterface
    {
        return new CreateArticleDto(
            !isset($data['title']) ? 'Titre de l\'article' : $data['title'],
            $data['content'] ?? 'Contenu de test',
            $data['shortContent'] ?? 'ShortContent',
            $data['enabled'] ?? false,
            $data['user'] ?? 1
        );
    }

    public function testCreateArticleDtoValid(): void
    {
        $this->assertValidationErrors($this->getArticleDto(), []);
    }

    #[DataProvider('provideTitleData')]
    public function testCreateArticleTitle(string $title, array $expectedErrors, bool $loadFixtures = false): void
    {
        if ($loadFixtures) {
            $this->databaseTool->loadAliceFixture([
                \dirname(__DIR__) . '/Fixtures/ArticleFixtures.yaml'
            ]);
        }

        $this->assertValidationErrors(
            $this->getArticleDto([
                'title' => $title,
            ]),
            $expectedErrors
        );
    }

    #[DataProvider('provideContentData')]
    public function testCreateArticleContent(string $content, array $expectedErrors): void
    {
        $this->assertValidationErrors(
            $this->getArticleDto([
                'content' => $content,
            ]),
            $expectedErrors
        );
    }

    #[DataProvider('provideShortContentData')]
    public function testCreateArticleShortContent(string $shortContent, array $expectedErrors): void
    {
        $this->assertValidationErrors(
            $this->getArticleDto([
                'shortContent' => $shortContent,
            ]),
            $expectedErrors
        );
    }


    public static function provideTitleData(): array
    {
        return [
            'no unique' => [
                'title' => 'Article de test',
                'expectedErrors' => [
                    'property' => 'title',
                    'code' => self::UNIQUE_ERROR_CODE
                ],
                'loadFixtures' => true,
            ],
            'min title' => [
                'title' => "a",
                'expectedErrors' => [
                    'property' => 'title',
                    'code' => self::MIN_LENGTH_ERROR_CODE,
                ],
            ],
            'max title' => [
                'title' => str_repeat('a', 256),
                'expectedErrors' => [
                    'property' => 'title',
                    'code' => self::MAX_LENGTH_ERROR_CODE,
                ],
            ],
            'not blank' => [
                'title' => '',
                'expectedErrors' => [
                    'property' => 'title',
                    'code' => self::NOT_BLANK_ERROR_CODE
                ]
            ]
        ];
    }

    public static function provideContentData(): array
    {
        return [
            'not blank' => [
                'content' => '',
                'expectedErrors' => [
                    'property' => 'content',
                    'code' => self::NOT_BLANK_ERROR_CODE
                ]
            ],
            'min length' => [
                'content' => 'e',
                'expectedErrors' => [
                    'property' => 'content',
                    'code' => self::MIN_LENGTH_ERROR_CODE,
                ]
            ]
        ];
    }

    public static function provideShortContentData(): array
    {
        return [
            'not blank' => [
                'shortContent' => '',
                'expectedErrors' => [
                    'property' => 'shortContent',
                    'code' => self::NOT_BLANK_ERROR_CODE
                ]
            ],
            'min length' => [
                'shortContent' => 'e',
                'expectedErrors' => [
                    'property' => 'shortContent',
                    'code' => self::MIN_LENGTH_ERROR_CODE,
                ]
            ],
            'max length' => [
                'shortContent' => str_repeat('a', 256),
                'expectedErrors' => [
                    'property' => 'shortContent',
                    'code' => self::MAX_LENGTH_ERROR_CODE,
                ]
            ]
        ];
    }
}
