<?php

namespace App\Message;

use App\Entity\Telephone;

final class CreateUserMessage
{
    private string $name;
    private string $email;
    private array $telephones;

     public function __construct(string $name, string $email, array $telephones)
     {
         $this->name = $name;
         $this->email = $email;
         $this->telephones = $telephones;
     }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName( string $value ):void
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

    public function addTelephones( array $number ):void
    {
        $this->telephones[] = new Telephone($number, $this);
    }

}
