<?php
/*
 * Import Export Class
 */

/**
 * Import Export Class
 * 
 * @since Types 1.2
 * @package Types
 * @subpackage Import Export
 * @version 0.1
 * @category core
 * @author srdjan <srdjan@icanlocalize.com>
 */
class WPCF_Import_Export
{

    /**
     * Meta keys that are used to generate checksum.
     * 
     * @var type 
     */
    var $group_meta_keys = array(
        '_wp_types_group_terms',
        '_wp_types_group_post_types',
        '_wp_types_group_fields',
        '_wp_types_group_templates',
        '_wpcf_conditional_display',
    );

    /**
     * Restricted data - ommited from checksum, applies to all content types.
     * 
     * @var type 
     */
    var $_remove_data_keys = array('id', 'ID', 'menu_icon', 'wpml_action',
        'wpcf-post-type', 'wpcf-tax', 'hash', 'checksum', '__types_id', '__types_title');

    /**
     * Required Group meta keys
     * 
     * @todo Make sure only this is used to fetch required meta_keys
     * @return type
     */
    function get_group_meta_keys() {
        return $this->group_meta_keys;
    }

    /**
     * Fetches required meta ny meta_key
     * 
     * @param type $group_id
     * @return type
     */
    function get_group_meta( $group_id ) {

        $_meta = array();
        $group = wpcf_admin_fields_get_group( $group_id );

        if ( !empty( $group ) ) {
            $meta = get_post_custom( $group['id'] );

            if ( !empty( $meta ) ) {
                foreach ( $meta as $meta_key => $meta_value ) {
                    if ( in_array( $meta_key, $this->group_meta_keys
                            )
                    ) {
                        $_meta[$meta_key] = $meta_value[0];
                    }
                }
            }
        }

        return $_meta;
    }

    /**
     * Generates checksums for defined content types.
     * 
     * @param type $type
     * @param type $item_id
     * @return type
     */
    function generate_checksum( $type, $item_id = null ) {
        switch ( $type ) {
            case 'group':
                $checksum = wpcf_admin_fields_get_group( $item_id );
                $checksum['meta'] = $this->get_group_meta( $item_id );
                ksort( $checksum['meta'] );
                break;

            case 'field':
                $checksum = wpcf_admin_fields_get_field( $item_id );
                break;

            case 'custom_post_type':
                $checksum = wpcf_get_custom_post_type_settings( $item_id );
                $checksum['relationship_settings']['has'] = wpcf_pr_get_has( $item_id );
                if ( is_array( $checksum['relationship_settings']['has'] ) ) {
                    ksort( $checksum['relationship_settings']['has'] );
                }
                $checksum['relationship_settings']['belongs'] = wpcf_pr_get_belongs( $item_id );
                if ( is_array( $checksum['relationship_settings']['belongs'] ) ) {
                    ksort( $checksum['relationship_settings']['belongs'] );
                }
                break;

            case 'custom_taxonomy':
                $checksum = wpcf_get_custom_taxonomy_settings( $item_id );
                break;

            default:
                /*
                 * Enable $this->generate_checksum('test');
                 */
                $checksum = $type;
                break;
        }

        // Unset various not wanted data
        foreach ( $this->_remove_data_keys as $key ) {
            if ( isset( $checksum[$key] ) ) {
                unset( $checksum[$key] );
            }
        }

        if ( is_array( $checksum ) ) {
            ksort( $checksum );
        }

//        debug( $checksum, false );

        return md5( maybe_serialize( $checksum ) );
    }

    /**
     * Generates and compares checksums.
     * 
     * @param type $type
     * @param type $item_id
     * @param type $import_checksum Imported checksum
     * @return type
     */
    function checksum( $type, $item_id, $import_checksum ) {
        // Generate checksum of installed content
        $checksum = $this->generate_checksum( $type, $item_id );
        // Compare
        return $checksum == strval( $import_checksum );
    }

    /**
     * Checks if item exists.
     * 
     * @param type $type
     * @param type $item_id
     * @return boolean
     */
    function item_exists( $type, $item_id ) {
        switch ( $type ) {
            case 'group':
                $check = wpcf_admin_fields_get_group( $item_id );
                break;

            case 'field':
                $check = wpcf_admin_fields_get_field( $item_id );
                break;

            case 'custom_post_type':
                $check = wpcf_get_custom_post_type_settings( $item_id );
                break;

            case 'custom_taxonomy':
                $check = wpcf_get_custom_taxonomy_settings( $item_id );
                break;

            default:
                return false;
                break;
        }
        return !empty( $check );
    }

}
