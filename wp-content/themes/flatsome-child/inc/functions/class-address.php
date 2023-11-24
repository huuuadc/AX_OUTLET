<?php

namespace OMS;
class ADDRESS {

    /**
     * @param $code
     * @return string
     */
    public function get_city_name_by_code($code){
         $city = $this->get_city_by('tiki_code', $code);

         if(!$city){
             return '';
         }

         return $city->province_name;
    }

    /**
     * @param $code
     * @return string
     */

    public function get_district_name_by_code($code){
        $district = $this->get_district_by('tiki_code', $code);

        if(!$district){
            return '';
        }

        return $district->district_name;
    }

    /**
     * @param $code
     * @return string
     */

    public function get_ward_name_by_code($code){
        $ward = $this->get_ward_by('tiki_code', $code);

        if(!$ward){
            return '';
        }

        return $ward->ward_name;
    }

    public function get_full_address_name_by_code($ward_code,$district_code,$city_code){
        return $this->get_ward_name_by_code($ward_code)
            . ', '. $this->get_district_name_by_code($district_code)
            . ', '. $this->get_city_name_by_code($city_code);
    }

    /**
     * @param $field
     * @param $value
     * @return array|false|object|\stdClass
     */

    public function get_city_by($field, $value){

        global $wpdb;

        // 'ID' is an alias of 'id'.
        if ( 'ID' === $field ) {
            $field = 'id';
        }

        if ( 'id' === $field ) {
            // Make sure the value is numeric to avoid casting objects, for example,
            // to int 1.
            if ( ! is_numeric( $value ) ) {
                return false;
            }
            $value = (int) $value;
            if ( $value < 1 ) {
                return false;
            }
        } else {
            $value = trim( $value );
        }

        if ( ! $value ) {
            return false;
        }

        $city = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}woocommerce_province WHERE $field = %s LIMIT 1",
                $value
            )
        );

        if ( ! $city ) {
            return false;
        }

        return $city;

    }

    /**
     * @param $field
     * @param $value
     * @return array|false|object|\stdClass
     */
    public function get_district_by($field, $value){

        global $wpdb;

        // 'ID' is an alias of 'id'.
        if ( 'ID' === $field ) {
            $field = 'id';
        }

        if ( 'id' === $field ) {
            // Make sure the value is numeric to avoid casting objects, for example,
            // to int 1.
            if ( ! is_numeric( $value ) ) {
                return false;
            }
            $value = (int) $value;
            if ( $value < 1 ) {
                return false;
            }
        } else {
            $value = trim( $value );
        }

        if ( ! $value ) {
            return false;
        }

        $district = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}woocommerce_district WHERE $field = %s LIMIT 1",
                $value
            )
        );

        if ( ! $district ) {
            return false;
        }

        return $district;

    }

    /**
     * @param $field
     * @param $value
     * @return array|false|object|\stdClass
     */
    public function get_ward_by($field, $value){

        global $wpdb;

        // 'ID' is an alias of 'id'.
        if ( 'ID' === $field ) {
            $field = 'id';
        }

        if ( 'id' === $field ) {
            // Make sure the value is numeric to avoid casting objects, for example,
            // to int 1.
            if ( ! is_numeric( $value ) ) {
                return false;
            }
            $value = (int) $value;
            if ( $value < 1 ) {
                return false;
            }
        } else {
            $value = trim( $value );
        }

        if ( ! $value ) {
            return false;
        }

        $ward = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}woocommerce_ward WHERE $field = %s LIMIT 1",
                $value
            )
        );

        if ( ! $ward ) {
            return false;
        }

        return $ward;

    }


}