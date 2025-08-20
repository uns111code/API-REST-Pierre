<?php

namespace App\Controller;

use App\Dto\User\RegisterUserDto;
use App\Mapper\UserMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Json;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserMapper $userMapper,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload]
        RegisterUserDto $dto,
    ): JsonResponse {
        $user = $this->userMapper->map($dto);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(
            [
                'id' => $user->getId(),
            ],
            Response::HTTP_CREATED
        );
    }
}