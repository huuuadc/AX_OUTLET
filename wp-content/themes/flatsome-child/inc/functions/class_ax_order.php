<?php

use AX\ADDRESS;

/**
 *
 */
class AX_ORDER extends WC_Order{

    private string $billing_city_code;
    private string $billing_district_code;
    private string $billing_ward_code;
    private $ax_address ;

    function __construct($order = 0)
    {
        parent::__construct($order);
        $this->set_meta_address_order();
        $this->ax_address = new ADDRESS();

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

    /**
     *
     * @return void
     */

    private function set_meta_address_order()
    {

        $this->billing_city_code = $this->get_billing_city();
        $this->billing_district_code = $this->get_meta('_billing_district');
        $this->billing_ward_code = $this->get_meta('_billing_ward');
        $address_more = array(
            'district'  =>  $this->billing_district_code,
            'ward'      =>  $this->billing_ward_code,
        );

        $this->data['billing'] = array_merge($this->data['billing'],$address_more);

        apply_filters('ax_add_more_billing_info', $this->data);

    }

    public function get_billing_city_code(){
        return $this->get_billing_city();
    }
    public function get_billing_city_name(){
        return $this->ax_address->get_city_name_by_code($this->billing_city_code);
    }
    public function get_billing_district_code(){
        return $this->get_billing_city();
    }
    public function get_billing_district_name(){
        return $this->ax_address->get_district_name_by_code($this->billing_district_code);
    }
    public function get_billing_ward_code(){
        return $this->billing_ward_code;
    }

    public function get_billing_ward_name(){
        return $this->ax_address->get_ward_name_by_code($this->billing_ward_code);
    }

    public function get_billing_address_full(){
        return $this->ax_address->get_full_address_name_by_code($this->billing_ward_code,$this->billing_district_code,$this->billing_city_code) ;
    }

}