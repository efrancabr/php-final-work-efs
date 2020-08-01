<?php
namespace App\MessageHandler;

use App\Entity\User;
use App\Message\ChangeUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ChangeUserMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $manager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->manager = $manager;
    }

    public function __invoke( ChangeUserMessage $message)
    {
        $user = $this->manager->getRepository(User::class)->find($message->getUserId());

        if (null === $user) {
            throw new \InvalidArgumentException('User with ID #' . $message->getUserId() . ' not found');
        }

        $user->setName( $message->getName() );
        $user->setEmail( $message->getEmail() );
        $user->addTelephone( '12' );

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
    }
}
