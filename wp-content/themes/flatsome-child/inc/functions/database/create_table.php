<?php

use OMS\TIKI_API;

/**
 *
 * Create table province, district, ward
 *
 * @return void
 */
function create_address_shipment(): bool
{
    try {

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        global $wpdb;
        global $create_address_shipment;
        $create_address_shipment = "1.0.1";

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        if (add_option('create_address_shipment',$create_address_shipment)){
            $installed_version = '0.0.0';
        }else {
            $installed_version = get_option('create_address_shipment');
        }

        if( $installed_version != $create_address_shipment){

            $tables = "
                    CREATE TABLE {$wpdb->prefix}woocommerce_province (
                        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        province_id bigint(4) NOT NULL,
                        province_name longtext NOT NULL,
                        enable boolean not null default 0,
                        tiki_code longtext NOT NULL,
                        PRIMARY KEY  (id),
                        UNIQUE KEY province_id (province_id)
                    ) $collate;        
                    CREATE TABLE {$wpdb->prefix}woocommerce_district (
                        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        code longtext NOT NULL,
                        district_id bigint(4) NOT NULL,
                        district_name longtext NOT NULL,
                        enable boolean not null default 0,
                        tiki_code longtext NOT NULL,
                        PRIMARY KEY  (id),
                        UNIQUE KEY district_id (district_id)
                    ) $collate;        
                    CREATE TABLE {$wpdb->prefix}woocommerce_ward (
                        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        code longtext NOT NULL,
                        ward_id bigint(4) NOT NULL,
                        ward_name longtext NOT NULL,
                        enable boolean not null default 0,
                        tiki_code longtext NOT NULL,
                        geo_lat float(10,7) NOT NULL,
                        geo_long float(10,7) NOT NULL,
                        PRIMARY KEY  (id),
                        UNIQUE KEY ward_id (ward_id)
                    ) $collate;        
            
            ";
            dbDelta($tables);

            update_option('create_address_shipment',$create_address_shipment);

        }

        $api_tiki = new TIKI_API();

        $data_region = $api_tiki->get_regions_tiki();

        $table_region = $wpdb->prefix.'woocommerce_province';
        $table_district = $wpdb->prefix.'woocommerce_district';
        $table_ward = $wpdb->prefix.'woocommerce_ward';

        foreach ( $data_region as $value){
            $data_region_insert = array(
                'province_id'   =>  $value->id,
                'province_name' =>  $value->name,
                'enable'        =>  $value->enable,
                'tiki_code'     =>  $value->tiki_code
            );
            $format = array('%d','%s','%d','%s');

            $wpdb->insert( $table_region, $data_region_insert, $format );

            $data_district = $api_tiki->get_districts_with_region_tiki($value->id);
            foreach ($data_district as $value_district){
                $data_district_insert = array(
                    'code'              =>  $value_district->code,
                    'district_id'       =>  $value_district->id,
                    'district_name'     =>  $value_district->name,
                    'enable'            =>  $value_district->enable,
                    'tiki_code'         =>  $value_district->tiki_code
                );
                $format = array('%s','%d','%s','%d','%s');

                $wpdb->insert( $table_district, $data_district_insert, $format );

                $data_ward = $api_tiki->get_wards_with_region_district_tiki($value->id,$value_district->id);

                foreach ($data_ward as $value_ward){
                    $data_ward_insert = array(
                        'code'              =>  $value_ward->code,
                        'ward_id'           =>  $value_ward->id,
                        'ward_name'         =>  $value_ward->name,
                        'enable'            =>  $value_ward->enable,
                        'tiki_code'         =>  $value_ward->tiki_code,
                        'geo_lat'           =>  $value_ward->geo_lat,
                        'geo_long'          =>  $value_ward->geo_long
                    );
                    $format = array('%s','%d','%s','%d','%s','%f','%f');

                    $wpdb->insert( $table_ward, $data_ward_insert, $format );
                }
            }
        }

        return true;

    } catch (\Throwable $th){
        write_log('Error: '. $th->getMessage());
        return false;
    }

}



