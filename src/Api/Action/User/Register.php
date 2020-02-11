<?php

declare(strict_types=1);

namespace App\Api\Action\User;

use App\Api\Action\RequestTransformer;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Register
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/users/register", methods={"POST"}, name="user_register")
     *
     * @throws \Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        $token = $this->userService->create(
            RequestTransformer::getRequiredField($request, 'name'),
            RequestTransformer::getRequiredField($request, 'email'),
            RequestTransformer::getRequiredField($request, 'password')
        );

        return new JsonResponse(['token' => $token], JsonResponse::HTTP_CREATED);
    }
}
