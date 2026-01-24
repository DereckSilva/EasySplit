<?php

namespace App\DTO;

class ExpenseDTO
{

    public string $description;
    public float $priceTotal;
    public int $parcels;
    public int $payerId;
    public string $paymentDate;

    public bool $intermediary;
    public string $intermediaries;
    public string $maturity;
    public bool $receiveNotification;

    public function __construct(array $data) {
        $this->description         = $data['description'];
        $this->priceTotal          = $data['price_total'];
        $this->parcels             = $data['parcels'];
        $this->payerId             = $data['payer_id'];
        $this->paymentDate         = $data['payment_date'];
        $this->intermediary        = $data['intermediary'];
        $this->intermediaries      = $data['intermediaries'];
        $this->maturity            = $data['maturity'];
        $this->receiveNotification = $data['receive_notification'];
    }

    public function toArray(): array {
        return [
            'description'          => $this->description,
            'price_total'          => $this->priceTotal,
            'parcels'              => $this->parcels,
            'payer_id'             => $this->payerId,
            'payment_date'         => $this->paymentDate,
            'intermediary'         => $this->intermediary,
            'intermediaries'       => $this->intermediaries,
            'maturity'             => $this->maturity,
            'receive_notification' => $this->receiveNotification,
        ];
    }

    public function toResponse(): array {
        return [
            'description'          => $this->description,
            'price_total'          => $this->priceTotal,
            'parcels'              => $this->parcels,
            'payer_id'             => $this->payerId,
            'payment_date'         => $this->paymentDate,
            'intermediary'         => $this->intermediary,
            'intermediaries'       => $this->intermediaries,
            'maturity'             => $this->maturity,
            'receive_notification' => $this->receiveNotification,
        ];
    }

}
