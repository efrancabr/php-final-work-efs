<?php

namespace App\Message;

use App\Entity\Telephone;

final class ChangeUserMessage
{
    private int $userId;
    private string $name;
    private string $email;
    private array $telephones;

    public function __construct( int $userId, string $name, string $email, array $telephones )
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->telephones = $telephones;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName( string $value  ):void
    {
        $this->name = $value;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail( string $value ):void
    {
        $this->email = $value;
    }

    public function getTelephones() : array
    {
        return $this->telephones;
    }

    public function addTelephone( array $number ) :void
    {
        $this->telephones[] = new Telephone($number, $this);
    }
}