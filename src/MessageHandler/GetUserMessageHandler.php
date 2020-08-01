<?php

namespace App\MessageHandler;

use App\Entity\Telephone;
use App\Entity\User;
use App\Message\GetUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetUserMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $manager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $manager,ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->manager = $manager;
    }


    public function __invoke( GetUserMessage $message)
    {
        $user = $this->manager->getRepository(User::class)->find($message->getUserId());

        if (null === $user) {
            throw new \InvalidArgumentException('User with ID #' . $message->getUserId() . ' not found');
        }

        return new JsonResponse($this->userToArray($user));

    }

    private function userToArray(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'telephones' => array_map(fn(Telephone $telephone) => [
                'number' => $telephone->getNumber()
            ], $user->getTelephones()->toArray())
        ];
    }


}
