<?php
namespace OMS;

use \OMS\ADDRESS;
class OMS_USER extends \WP_User {

    private string $address_1;
    private string $ward_code;
    private string $district_code;
    private string $city_code;
    private ADDRESS $address;

    public function __construct($id = 0, $name = '', $site_id = '')
    {
        parent::__construct($id, $name, $site_id);

        if ($this->ID != 0){
            $this->address_1 = get_user_meta($this->ID,'billing_address_1',true);
            $this->ward_code = get_user_meta($this->ID,'billing_ward',true);
            $this->district_code = get_user_meta($this->ID,'billing_district',true);
            $this->city_code = get_user_meta($this->ID,'billing_city',true);
            $this->address = new ADDRESS();
        }

    }

    /**
     * @param $ward_code
     * @return true
     */
    public function set_ward($ward_code){
        update_user_meta( $this->ID, 'billing_ward', $ward_code );
        return true;
    }

    /**
     * @param $district_code
     * @return true
     */
    public function set_district($district_code){
        update_user_meta( $this->ID, 'billing_district', $district_code );
        return true;
    }

    /**
     * @return string
     */
    public function get_ward(){
        return $this->address->get_ward_name_by_code($this->ward_code);
    }

    /**
     * @return string
     */
    public function get_district(){
        return $this->address->get_district_name_by_code($this->district_code);
    }

    /**
     * @return string
     */
    public function get_city(){
        return $this->address->get_city_name_by_code($this->city_code);
    }

    /**
     * @return string
     */
    public function get_full_address(){
        return $this->address_1 .', '. $this->address->get_full_address_name_by_code($this->ward_code,$this->district_code,$this->city_code);
    }

}