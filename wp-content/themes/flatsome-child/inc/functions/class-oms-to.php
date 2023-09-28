<?php

namespace OMS;

class OMS_TO extends \WC_Order
{

    public function __construct($order = 0)
    {
        parent::__construct($order);
    }

    public function get_type(): string
    {
        return 'transfer_order';
    }

}