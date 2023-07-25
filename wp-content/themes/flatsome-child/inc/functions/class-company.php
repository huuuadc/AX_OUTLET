<?php

namespace AX;

use AX\ADDRESS;

class COMPANY {
    private string $name;
    private string $email;
    private string $phone;
    private string $zone;
    private string $country_code;
    private string $city_code;
    private string $district_code;
    private string $ward_code;

    private string $address;

    private object $ax_address;

    function __construct()
    {
        $this->name = get_option('web_company_name') ?? '';
        $this->email = get_option('web_company_email') ?? '';
        $this->phone = get_option('web_company_phone') ?? '';
        $this->zone = get_option('web_company_zone') ?? '';
        $this->country_code = get_option('web_company_country') ?? '';
        $this->city_code = get_option('web_company_city') ?? '';
        $this->district_code = get_option('web_company_district') ?? '';
        $this->ward_code = get_option('web_company_ward') ?? '';
        $this->address = get_option('web_company_address') ?? '';
        $this->ax_address = new ADDRESS();

    }

    public function get_company_name(){
        return $this->name;
    }
    public function get_company_email(){
        return $this->email;
    }
    public function get_company_phone(){
        return $this->phone;
    }
    public function get_company_country_code(){
        return $this->country_code;
    }
    public function get_company_city_code(){
        return $this->city_code;
    }
    public function get_company_district_code(){
        return $this->city_code;
    }
    public function get_company_ward_code(){
        return $this->ward_code;
    }

    public function get_company_address(){
        return $this->address;
    }

    /**
     * @return string
     */

    public function get_company_country_name(): string
    {
        return 'Việt Nam';
    }

    /**
     * @return string
     */
    public function get_company_city_name(){
        return $this->ax_address->get_city_name_by_code($this->city_code);
    }

    /**
     * @return string
     */
    public function get_company_district_name(){
        return $this->ax_address->get_district_name_by_code($this->district_code);
    }

    /**
     * @return string
     */
    public function get_company_ward_name(){
        return $this->ax_address->get_ward_name_by_code($this->ward_code);
    }

    /**
     * @return string
     */
    public function get_company_full_address_by_code(){
        return $this->ax_address->get_full_address_name_by_code($this->ward_code,$this->district_code,$this->city_code);
    }



}
