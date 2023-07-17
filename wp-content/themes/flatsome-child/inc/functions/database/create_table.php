<?php

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

        if (!get_option('create_address_shipment')){
            add_option('create_address_shipment',$create_address_shipment);
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

        return true;

    } catch (\Throwable $th){
        write_log('Error: '. $th->getMessage());
        return false;
    }

}

