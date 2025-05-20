<?php

namespace App\Controller;

use App\Mapper\UserMapper;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\Interfaces\UserRequestInterface;
use App\Dto\User\RegisterUserDto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    ): JsonResponse
    {

        // dd($dto);
        $user = $this->userMapper->map($dto);
        // dd($user);
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