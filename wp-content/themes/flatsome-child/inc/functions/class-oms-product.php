<?php

class OMS_Product extends WC_Product{

    public function __construct($product = 0)
    {
        parent::__construct($product);
    }

    public function update_id_platform($id_platform= ''):bool
    {
        if (update_post_meta($this->get_id(),'id_platform',$id_platform)) return true;
        return false;
    }

    public function get_id_platform()
    {
        return  $this->get_meta( 'id_platform');
    }

}