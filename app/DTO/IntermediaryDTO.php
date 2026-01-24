<?php

namespace App\DTO;

class IntermediaryDTO
{

    public $email;
    public $phoneNumber;

    public function __construct( $email, $phoneNumber ) {
        $this->email       = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function toArray(): array {
        return [
            'email'        => $this->email,
            'phone_number' => $this->phoneNumber,
        ];
    }

    public function toResponse(): array {
        return [
            'email'        => $this->email,
            'phone_number' => $this->phoneNumber,
        ];
    }

}
