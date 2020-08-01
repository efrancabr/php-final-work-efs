<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\CreateUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateUserMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $manager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->manager = $manager;
    }

    public function __invoke(CreateUserMessage $message  )
    {
        $name = $message->getName();
        $email = $message->getEmail();
        $telephones = $message->getTelephones();

        $user = new User( $name  , $email, $telephones  );

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $violations = array_map(fn(ConstraintViolationInterface $violation) => [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ], iterator_to_array($errors));

            return new JsonResponse($violations, Response::HTTP_BAD_REQUEST);
        }

        $this->manager->persist($user);
        $this->manager->flush();

        return new Response('', Response::HTTP_CREATED, [
            'Location' => '/users/' . $user->getId()
        ]);
    }

}
