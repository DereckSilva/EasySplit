<?php

namespace App\DTO;

use DateTime;

class UserDTO
{

    public $name;
    public $email;
    public $password;
    public $birthDate;
    public $phoneNumber;

    public function __construct( $name, $email, $password, $birthDate, $phoneNumber ) {
        $this->name        = $name;
        $this->email       = $email;
        $this->password    = $password;
        $this->birthDate   = $birthDate;
        $this->phoneNumber = $phoneNumber;
    }

    public function toArray(): array {
        return [
            'name'         => $this->name,
            'email'        => $this->email,
            'password'     => $this->password,
            'birthdate'    => $this->birthDate,
            'phone_number' => $this->phoneNumber,
        ];
    }

    public function toResponse(int $id, string $updated, string $created): array {
        return [
            'id'           => $id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone_number' => $this->phoneNumber,
            'birthdate'    => new DateTime($this->birthDate)->format('d/m/Y'),
            'update_at'    => new DateTime($updated)->format('d/m/Y H:i:s'),
            'created_at'   => new DateTime($created)->format('d/m/Y H:i:s')
        ];
    }
}
