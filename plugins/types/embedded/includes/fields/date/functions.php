<?php
/*
 * Various functions for Date Field.
 */

/**
 * Calculate time
 * 
 * Called from wpcf_fields_date_value_get_filter().
 * 
 * @param array $value Full data
 */
function wpcf_fields_date_calculate_time( $value ) {

    extract( $value );

    // Empty is only on new post
    if ( empty( $timestamp ) ) {
        return null;
    }

    // Fix hour and minute
    if ( strval( $hour ) == '00' ) {
        $hour = 0;
    }
    if ( strval( $minute ) == '00' ) {
        $minute = 0;
    }

    // Add Hour and minute
    $timestamp = intval( $timestamp ) + (60 * 60 * intval( $hour )) + (60 * intval( $minute ));

    return $timestamp;
}

/**
 * Converts date to time on post saving.
 * 
 * @todo SWITCH TO wpcf_fields_date_value_get_filter()
 * 
 * @param type $value
 * @return int timestamp 
 */
function wpcf_fields_date_value_save_filter( $value, $field, $field_object ) {

    if ( empty( $value ) ) {
        return $value;
    }

    // TODO Check Why we had this earlier (obsolete?)
    //    $date_format = wpcf_get_date_format();
//    if ($date_format == 'd/m/Y') {
//        // strtotime requires a dash or dot separator to determine dd/mm/yyyy format
//        $value = str_replace('/', '-', $value);
//    }
//    return strtotime(strval($value));

    /*
     * This is submitted data so we use datepicker as timestamp.
     * Do not use any other hook for this.
     */
    $value['timestamp'] = strtotime( strval( $value['datepicker'] ) );

    // Return timestamp
    return wpcf_fields_date_value_get_filter( $value, $field_object, 'timestamp' );
}

/**
 * Fix missing items.
 * 
 * @param type $value
 * @param type $field_object
 * @return type
 */
function wpcf_fields_date_get_submitted_data( $value, $field_object ) {
    // Return Full data
    if ( isset( $field_object->cf['type'] ) && $field_object->cf['type'] == 'date' ) {
        return wpcf_fields_date_value_get_filter( $value, $field_object,
                        'array', 'save' );
    }
    return $value;
}

/**
 * 
 * @param type $post_id
 * @param type $field_id
 * @return type
 */
function wpcf_fields_date_get_hour_and_minute( $post_id, $field_id ) {
    return get_post_meta(
                    $post_id, '_wpcf_' . $field_id . '_hour_and_minute', true );
}

/**
 * Collects after meta is created.
 * 
 * We'll be holding Hour and Minute in separate meta.
 * 
 * @param type $value
 * @param type $field
 * @param type $this
 * @param type $meta_id
 */
function wpcf_fields_date_collect_hour_and_minute( $value, $field,
        $field_object, $meta_id, $meta_value_original ) {
    global $wpcf;
    // Use Field in Loop
    if ( isset( $wpcf->field->cf['type'] ) && $wpcf->field->cf['type'] == 'date' ) {
        $wpcf->field->__date->additional_meta[$field['id']][$meta_id] = $meta_value_original;
    }
}

/**
 * Saves Hour and Minute after meta is created.
 * 
 * We'll be holding Hour and Minute in separate meta.
 * 
 * @param type $value
 * @param type $field
 * @param type $this
 * @param type $meta_id
 */
function wpcf_fields_date_save_hour_and_minute( $post_id, $field ) {
    global $wpcf;
    // Use Field in Loop
    if ( isset( $wpcf->field->cf['type'] ) && $wpcf->field->cf['type'] == 'date' ) {
        if ( !empty( $wpcf->field->__date->additional_meta[$field['id']] ) ) {
            update_post_meta(
                    $post_id, '_wpcf_' . $field['id'] . '_hour_and_minute',
                    $wpcf->field->__date->additional_meta[$field['id']] );
        }
    }
}

/**
 * Filters conditional display value for built-in Types Conditinal check.
 * 
 * @param type $value
 * @param type $field
 * @param type $operation
 * @param type $conditional_field
 * @param type $post
 * @return type 
 */
function wpcf_fields_date_conditional_value_filter( $value, $field, $operation,
        $field_compared, $post ) {

    $field = wpcf_admin_fields_get_field( $field );
    if ( !empty( $field ) && $field['type'] == 'date' ) {

        /*
         * 
         * 
         * 
         * 
         * Here we need to determine data
         */

        $_field = new WPCF_Field();
        $_field->set( $post, $field );

        return wpcf_fields_date_value_get_filter( $value, $_field, 'timestamp',
                        'skip_hour_and_minute' );

        // TODO Date revise why needed.
        // Check dates
        $value = wpv_filter_parse_date( $value );
    }
    return $value;
}

/**
 * Add post meta hook if Custom Conditinal Statement used.
 * 
 * @param type $field
 */
function wpcf_fields_custom_conditional_statement_hook( $field ) {
    if ( isset( $field->cf['type'] ) && $field->cf['type'] == 'date' ) {
        // Enqueue after first filters in evaluate.php
        add_filter( 'get_post_metadata',
                'wpcf_fields_date_custom_conditional_statement_filter', 20, 4 );
    }
}

/**
 * Custom Conditinal Statement hook returns timestamp if array.
 * 
 * NOTE that $null is already filtered to use $_POST values
 * at priority 10.
 * 
 * @param type $null
 * @param type $object_id
 * @param type $meta_key
 * @param type $single
 * @return mixed timestamp or $null
 */
function wpcf_fields_date_custom_conditional_statement_filter( $null,
        $object_id, $meta_key, $single ) {

    $post = get_post( $object_id );

    if ( !empty( $post->ID ) ) {

        $field = wpcf_admin_fields_get_field( $single );
        $field->set( $post, $meta_key );

        if ( isset( $field->cf['type'] ) && $field->cf['type'] == 'date' ) {
            $res = wpcf_fields_date_value_get_filter( $null, $field,
                    'timestamp', 'skip_hour_and_minute' );
            if ( is_int( $res ) ) {
                return $res;
            }
        }
    }

    return $null;
}

/**
 * Returns most suitable date format.
 * 
 * @global type $supported_date_formats
 * @return string
 */
function wpcf_get_date_format() {
    global $supported_date_formats;

    $date_format = get_option( 'date_format' );
    if ( !in_array( $date_format, $supported_date_formats ) ) {
        // Choose the Month day, Year fromat
        $date_format = 'F j, Y';
    }

    return $date_format;
}

/*
 * 
 * 
 *  TODO DOCUMENT
 */

function wpcf_get_date_format_text() {
    global $supported_date_formats, $supported_date_formats_text;

    $date_format = get_option( 'date_format' );
    if ( !in_array( $date_format, $supported_date_formats ) ) {
        // Choose the Month day, Year fromat
        $date_format = 'F j, Y';
    }

    return $supported_date_formats_text[$date_format];
}

function _wpcf_date_convert_wp_to_js( $date_format ) {
    $date_format = str_replace( 'd', 'dd', $date_format );
    $date_format = str_replace( 'j', 'd', $date_format );
    $date_format = str_replace( 'l', 'DD', $date_format );
    $date_format = str_replace( 'm', 'mm', $date_format );
    $date_format = str_replace( 'n', 'm', $date_format );
    $date_format = str_replace( 'F', 'MM', $date_format );
    $date_format = str_replace( 'Y', 'yy', $date_format );

    return $date_format;
}

/**
 *
 * Convert a format from date() to strftime() format
 *
 */
function wpcf_date_to_strftime( $format ) {

    $format = str_replace( 'd', '%d', $format );
    $format = str_replace( 'D', '%a', $format );
    $format = str_replace( 'j', '%e', $format );
    $format = str_replace( 'l', '%A', $format );
    $format = str_replace( 'N', '%u', $format );
    $format = str_replace( 'w', '%w', $format );

    $format = str_replace( 'W', '%W', $format );

    $format = str_replace( 'F', '%B', $format );
    $format = str_replace( 'm', '%m', $format );
    $format = str_replace( 'M', '%b', $format );
    $format = str_replace( 'n', '%m', $format );

    $format = str_replace( 'o', '%g', $format );
    $format = str_replace( 'Y', '%Y', $format );
    $format = str_replace( 'y', '%y', $format );

    return $format;
}

/**
 * Sets data for Hour and Minute.
 * 
 * @param type $value
 * @param type $date_format
 * @return int
 */
//function wpcf_fields_date_set_hour_and_minute( $value ) {
//    $date_format = wpcf_get_date_format();
//    $data = array();
//    if ( is_array( $value ) ) {
//        if ( $date_format == 'd/m/Y' ) {
//            // strtotime requires a dash or dot separator to determine dd/mm/yyyy format
//            $value['datepicker'] = str_replace( '/', '-',
//                    strval( $value['datepicker'] ) );
//        }
//        $data['datepicker'] = strtotime( $value['datepicker'] );
//        $data['hour'] = isset( $value['hour'] ) ? intval( $value['hour'] ) : 8;
//        $data['minute'] = isset( $value['minute'] ) ? intval( $value['minute'] ) : 0;
//    } else {
//        if ( $date_format == 'd/m/Y' ) {
//            // strtotime requires a dash or dot separator to determine dd/mm/yyyy format
//            $value = str_replace( '/', '-', strval( $value ) );
//        }
//        // Check if date string
//        $_v = strtotime( $value );
//        $data['datepicker'] = $_v == false || $_v == -1 ? $value : $_v;
//        $data['hour'] = 8;
//        $data['minute'] = 0;
//    }
//
//    return $data;
//}

/**
 * 
 * @param type $meta_value
 * @param type $params
 * @param type $post_id
 * @param type $field_id
 * @param type $meta_id
 * @return type
 */
//function wpcf_fields_date_get_hour_and_minute_by_meta_key( $meta_value, $params,
//        $post_id, $field_id, $meta_id ) {
//    $meta = get_post_meta(
//            $post_id, '_wpcf_' . $field_id . '_hour_and_minute', true );
//    return isset( $meta[$meta_id] ) ? $meta[$meta_id] : $meta_value;
//}

/**
 * String to time.
 * 
 * @param type $posted
 * @param type $field
 * @return type
 */
//function wpcf_fields_date_to_time( $value, $field ) {
//    if ( isset( $field->cf['type'] ) && $field->cf['type'] == 'date' ) {
//        return wpcf_fields_date_value_get_filter( $value, $field, 'timestamp' );
//    }
//    return $value;
//}