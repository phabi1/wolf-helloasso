<?php

namespace Wolf\HelloAsso\Sdk\Model;

use Wolf\HelloAsso\Sdk\Model\Payer;

class Order
{
    public $amount_cents;
    public $item_name;
    public $terms = [];
    public $back_url;
    public $error_url;
    public $return_url;
    public Payer $payer;
    public $metadata;

    public function __construct()
    {
        $this->payer = new Payer();
    }

}