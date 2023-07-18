<?php

/**
 *
 */
class AX_ORDER extends WC_Order{
    function __construct($order = 0)
    {
        parent::__construct($order);
    }

    /**
     *
     * @return string
     */
    public function get_ax_address()
    {
        $address = $this->get_billing_address_1() . ',' . $this->get_billing_city();
        return  $address;
    }

}