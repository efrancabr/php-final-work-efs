<?php

namespace App\Controller;

use App\Entity\Telephone;
use App\Entity\User;
use App\Message\CreateUserMessage;
use App\Message\ChangeUserMessage;
use App\Message\GetUserMessage;
use App\Message\RemoveUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    private MessageBusInterface $bus;

    private ValidatorInterface $validator;
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator, \Symfony\Component\Messenger\MessageBusInterface $bus)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->bus = $bus;
    }

    /**
     * @Route("/users", methods={"GET"})
     */
    public function listAction(): Response
    {
        $users = $this->manager->getRepository(User::class)->findAll();

        $data = [];
        foreach ($users as $user) {
            $data[] = $this->userToArray($user);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/users/{id}", methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function detailAction(int $id): Response
    {
        $retorno =  $this->bus->dispatch(new  GetUserMessage( $id ));
        return new Response( '' , Response::HTTP_OK);
    }

    /**
     * @Route("/users", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $requestContent = $request->getContent();
        $json = json_decode($requestContent, true);

        $this->bus->dispatch(new  CreateUserMessage( $json['name'], $json['email'], $json['telephones'] ));
        return new Response('', Response::HTTP_OK);
    }

    /**
     * @Route("/users/{id}", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateAction(Request $request, int $id): Response
    {
        $requestContent = $request->getContent();
        $json = json_decode($requestContent, true);

        $this->bus->dispatch( new  ChangeUserMessage( $id, $json['name'], $json['email'], $json['telephones'] ));
        return new Response('', Response::HTTP_OK);
    }

    /**
     * @Route("/users/{id}", methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function removeAction(int $id): Response
    {
        $this->bus->dispatch(new RemoveUserMessage($id));
        return new Response('', Response::HTTP_OK);
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
