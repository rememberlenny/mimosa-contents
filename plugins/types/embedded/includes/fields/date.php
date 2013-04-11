<?php
/*
 * Date Field
 * 
 * TODO Datepicker value convert to date formatted string
 */

// Set date formats
require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields/date/date-formats.php';

// Include helper functions
require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields/date/functions.php';

// Include calendar
require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields/date/calendar.php';

// Include JS
require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields/date/js.php';

/*
 * 
 * TODO Document this! Bruce?
 */
if ( !function_exists( 'wpv_filter_parse_date' ) ) {
    require_once WPCF_EMBEDDED_ABSPATH . '/common/wpv-filter-date-embedded.php';
}

/*
 * 
 * 
 * 
 * 
 * 
 * Filters
 */
/*
 * 
 * 
 * This one is called to convert stored timestamp to array,
 * appending Hour and Minute data too.
 * 
 * Called from WPCF_Field:: _get_meta()
 * 
 * Returns array(
 *  'timestamp' => 14435346,
 *  'datepicker' => 'April 9, 2012',
 *  'hour' => 8,
 *  'minute' => 9
 */
add_filter( 'wpcf_fields_type_date_value_get',
        'wpcf_fields_date_value_get_filter', 10, 4 );

/*
 * 
 * Used to convert submitted data (array) to timestamp before saving field.
 * Called from WPCF_Field::_filter_save_value()
 * 
 * Returns timestamp
 */
add_filter( 'wpcf_fields_type_date_value_save',
        'wpcf_fields_date_value_save_filter', 10, 3 );
/*
 * Re-calculate data and return Full data:
 * array( $meta_id => array( timestamp, datepicker, hour, minute ) )
 */
add_filter( 'types_field_get_submitted_data',
        'wpcf_fields_date_get_submitted_data', 15, 2 );

/*
 * 
 * Action hook
 * 
 * Called from WPCF_Field::_action_save()
 * after each field is saved ( if Repeater - after each one )
 */
add_action( 'wpcf_fields_type_date_save',
        'wpcf_fields_date_collect_hour_and_minute', 10, 5 );

/*
 * 
 * 
 * Used in field-post.php after saving field.
 * After date field is saved ( all fields if Repeater )
 * 
 * 
 * save full data in separate meta field '_wpcf_' . $field . '_hour_and_minute'
 * array( $meta_id => array( timestamp, datepicker, hour, minute ) )
 */
add_action( 'wpcf_post_field_saved', 'wpcf_fields_date_save_hour_and_minute',
        10, 5 );

/*
 * TODO OBSOLETE! REMOVE
 * 
 * Fetches full data for Form
 * TODO SWITCH to wpcf_fields_date_value_get_filter()
 */
//add_filter( 'wpcf_fields_type_date_value_display',
//        'wpcf_fields_date_get_hour_and_minute_by_meta_key', 10, 5 );

/*
 * 
 * Built-in Types Conditinal check hook.
 * Used for Conditional value.
 * If array - convert to timestamp.
 * 
 * Use wpcf_fields_date_value_get_filter()
 * Returns timestamp
 */
add_filter( 'wpcf_conditional_display_compare_condition_value',
        'wpcf_fields_date_conditional_value_filter', 10, 5 );
/*
 * This only applied when Checking in AJAX call
 */
if ( defined( 'DOING_AJAX' ) ) {
    add_filter( 'wpcf_conditional_display_compare_meta_value',
            'wpcf_fields_date_conditional_value_filter', 10, 5 );
}

/*
 * 
 * This is added for Custom Conditional Statement.
 * Use more specific hook in evaluate.php
 */
add_action( 'types_custom_conditional_statement',
        'wpcf_fields_custom_conditional_statement_hook' );

/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_date() {
    return array(
        'id' => 'wpcf-date',
        'title' => __( 'Date', 'wpcf' ),
        'description' => __( 'Date', 'wpcf' ),
        'validate' => array('required', 'date'),
        'meta_box_js' => array(
            'wpcf-jquery-fields-date' => array(
                'src' => WPCF_EMBEDDED_RES_RELPATH . '/js/jquery.ui.datepicker.min.js',
                'deps' => array('jquery-ui-core'),
            ),
            'wpcf-jquery-fields-date-inline' => array(
                'inline' => 'wpcf_fields_date_meta_box_js_inline',
            ),
        ),
        'meta_box_css' => array(
            'wpcf-jquery-fields-date' => array(
                'src' => WPCF_EMBEDDED_RES_RELPATH . '/css/jquery-ui/datepicker.css',
            ),
        ),
        'meta_key_type' => 'TIME',
        'version' => '1.2',
    );
}

/**
 * From data for post edit page.
 * 
 * @param type $field 
 * @param type $data
 * @param type $field_object Field instance 
 */
function wpcf_fields_date_meta_box_form( $field, $field_object = null ) {

    /*
     * Added extra fields 'hour' and 'minute'.
     * 
     * If value is not array it is assumed that DB entry is timestamp()
     * and data is converted to array.
     */
    $value = $field['value'] = wpcf_fields_date_value_get_filter( $field['value'],
            $field_object );

    // TODO WPML
    if ( isset( $field['wpml_action'] ) && $field['wpml_action'] == 'copy' ) {
        $attributes = array('style' => 'width:150px;');
    } else {
        $attributes = array('class' => 'wpcf-datepicker', 'style' => 'width:150px;');
    }

    /*
     * 
     * Do not forget to trigger datepicker script
     * Only trigger on AJAX call (inserting new)
     */
    $js_trigger = defined( 'DOING_AJAX' ) ? '<script type="text/javascript">wpcfFieldsDateInit(\'\');</script>' : '';

    /*
     * 
     * 
     * Set Form
     */
    $unique_id = wpcf_unique_id( serialize( $field ) );
    $form = array();
    $form[$unique_id . '-datepicker'] = array(
        '#type' => 'textfield',
        '#title' => '&nbsp;' . $field['name'],
        '#attributes' => $attributes,
        '#name' => 'wpcf[' . $field['slug'] . '][datepicker]',
        '#id' => 'wpcf-date-' . $field['slug'] . '-datepicker-' . $unique_id,
        '#value' => $value['datepicker'],
        '#inline' => true,
        '#after' => '' . $js_trigger, // Append JS trigger
        '#_validate_this' => true, // Important when H and M are used too
    );

    /*
     * 
     * If set 'date_and_time' add time
     */
    if ( !empty( $field['data']['date_and_time'] ) && $field['data']['date_and_time'] == 'and_time' ) {
        $hours = 24;
        $minutes = 60;
        $options = array();

        // Hour
        for ( $index = 0; $index < $hours; $index++ ) {
            $prefix = $index < 10 ? '0' : '';
            $options[$index] = array(
                '#title' => $prefix . strval( $index ),
                '#value' => $index,
            );
        }
        $form[$unique_id . 'time_hour'] = array(
            '#type' => 'select',
            '#title' => __( 'Hour', 'wpcf' ),
            '#inline' => true,
            '#before' => '<br />',
            '#after' => '&nbsp;&nbsp;',
            '#options' => $options,
            '#default_value' => $value['hour'],
            '#name' => 'wpcf[' . $field['slug'] . '][hour]',
            '#id' => 'wpcf-date-' . $field['slug'] . '-select-hour-'
            . $unique_id,
            '#inline' => true,
        );

        // Minutes
        for ( $index = 1; $index < $minutes; $index++ ) {
            $prefix = $index < 10 ? '0' : '';
            $options[$index] = array(
                '#title' => $prefix . strval( $index ),
                '#value' => $index,
            );
        }
        $form[$unique_id . 'time_minute'] = array(
            '#type' => 'select',
            '#title' => __( 'Minute', 'wpcf' ),
            '#after' => '<br /><br />',
            '#inline' => true,
            '#options' => $options,
            '#default_value' => $value['minute'],
            '#name' => 'wpcf[' . $field['slug'] . '][minute]',
            '#id' => 'wpcf-date-' . $field['slug'] . '-minute-'
            . $unique_id,
        );
    }

    return $form;
}

/**
 * Parses date meta.
 * 
 * Use this as main function.
 * 
 * @uses wpcf_fields_date_calculate_time()
 * 
 * @param type $value
 * @param type $field Field data
 * $param string $return Specify to return array or specific element of same array
 *          ( timestamp, datepicker, hour, minute )
 * @return mixed array | custom parameter
 */
function wpcf_fields_date_value_get_filter( $value, $field, $return = 'array',
        $context = 'get' ) {

    global $wpcf;

    $value_cloned = $value;

    if ( is_array( $value ) ) {
        /*
         * See if missing timestamp and datepicker present
         */
        if ( (!isset( $value['timestamp'] ) || !is_int( $value['timestamp'] ))
                && isset( $value['datepicker'] ) ) {
            $_check = strtotime( strval( $value['datepicker'] ) );
            if ( $_check !== false ) {
                $value['timestamp'] = $_check;
            }
        }
        $value = wp_parse_args( $value,
                array(
            'timestamp' => null,
            'hour' => 8,
            'minute' => 0,
            'datepicker' => '',
                )
        );
    } else if ( empty( $value ) ) {
        return array(
            'timestamp' => null,
            'hour' => 8,
            'minute' => 0,
            'datepicker' => '',
        );
    } else {
        /*
         * strtotime() returns negative numbers like -49537390513
         * 
         * https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/160422568/comments
         * http://www.php.net/manual/en/function.strtotime.php
         * 
         * QUOTE
         * Returns a timestamp on success, FALSE otherwise.
         * Previous to PHP 5.1.0, this function would return -1 on failure. 
         * 
         * BUT on some hosts it returns negative numbers ( our test sites too )
         */
        if ( !is_numeric( $value ) ) {
            $_check = strtotime( $value );
            if ( $_check !== false && $_check > 1 ) {
                $value = $_check;
            }
        }
        $value = array(
            'timestamp' => intval( $value ),
            'hour' => 8,
            'minute' => 0,
            'datepicker' => '',
        );
    }

    $value['datepicker'] = trim( $value['datepicker'] );

    /*
     * Since Types 1.2 we require $cf field object
     */
    if ( $field instanceof WPCF_Field ) {
        $post = $field->post;
    } else {
        // Remove for moment
        remove_filter( 'wpcf_fields_type_date_value_get',
                'wpcf_fields_date_value_get_filter', 10, 4 );

        // Hide on frontpage where things will go fine because of loop
        if ( is_admin() ) {
            _deprecated_argument( 'date_obsolete_parameter', '1.2',
                    '<br /><br /><div class="wpcf-error">'
                    . 'Since Types 1.2 $cf field object is required' . '</div><br /><br />' );
        }
        /*
         * Set default objects
         */
        $_field = $field;
        $field = new WPCF_Field();
        $field->context = is_admin() ? 'frontend' : 'group';
        $post_id = wpcf_get_post_id( $field->context );
        $post = get_post( $post_id );
        if ( empty( $post ) ) {
            return $value;
        }
        $field->set( $post, $_field );

        // Back to filter
        add_filter( 'wpcf_fields_type_date_value_get',
                'wpcf_fields_date_value_get_filter', 10, 4 );
    }

    /*
     * Get hour and minute
     * We need meta_id here.
     * 
     * NOT Used for 'save' context.
     * We already have submitted data in $value
     */
    if ( !in_array( $context, array('save', 'skip_hour_and_minute') ) ) {
        if ( !empty( $post->ID ) ) {
            $_meta_id = isset( $_field['__meta_id'] ) ? $_field['__meta_id'] : $field->meta_object->meta_id;
            $_hm = get_post_meta( $post->ID,
                    '_wpcf_' . $field->cf['id']
                    . '_hour_and_minute', true );
            $hm = isset( $_hm[$_meta_id] ) ? $_hm[$_meta_id] : array();
        } else {
            /*
             * If $post is not set.
             * We need to record this
             */
            $wpcf->errors['missing_post'][] = func_get_args();
        }

        /*
         * Setup hour and minute.
         */
        if ( !empty( $hm ) && is_array( $hm )
                && (isset( $hm['hour'] ) && isset( $hm['minute'] ) ) ) {
            $value['hour'] = $hm['hour'];
            $value['minute'] = $hm['minute'];
        }
    }

    // Calculate time IF NOT SET ( otherwise it's same as main meta value )
    // Always when using 'get' context on frontend
    if ( (!is_admin() && $context == 'get')
            || (empty( $value['timestamp'] ) || !is_int( $value['timestamp'] ))
    ) {
        $value['timestamp'] = wpcf_fields_date_calculate_time( $value );
    }

    /*
     * Set datepicker to use formatted date IF DO NOT EXISTS
     * (otherwise it keeps Datepicker string like 'August 9, 2012'.
     * OR is not time string
     */
    if ( !empty( $value['timestamp'] ) && (empty( $value['datepicker'] )
            || strtotime( strval( $value['datepicker'] ) ) === false ) ) {
        $value['datepicker'] = date( wpcf_get_date_format(),
                intval( $value['timestamp'] ) );
    }

    $_return = $value;
    if ( $return != 'array' ) {
        if ( isset( $value[strval( $return )] ) ) {
            $_return = $value[strval( $return )];
        }
    }

    // Debug
    $wpcf->debug->dates[] = array(
        'original_value' => $value_cloned,
        'value' => $value,
        'return' => $_return,
        'field' => $field->cf,
        'context' => $context,
    );

    return $_return;
}

/**
 * View function.
 * 
 * @param type $params 
 */
function wpcf_fields_date_view( $params ) {

    global $wp_locale;

    // Append hour and minute if necessary
    $meta = wpcf_fields_date_value_get_filter( $params['field_value'],
            $params['field'] );
    $params['field_value'] = intval( $meta['timestamp'] );


    $defaults = array(
        'format' => get_option( 'date_format' ),
        'style' => '' // add default value
    );
    $params = wp_parse_args( $params, $defaults );
    $output = '';

    switch ( $params['style'] ) {
        case 'calendar':
            $output .= wpcf_fields_date_get_calendar( $params, true, false );
            break;

        default:
            $field_name = '';


            // Extract the Full month and Short month from the format.
            // We'll replace with the translated months if possible.
            $format = $params['format'];
            $format = str_replace( 'F', '#111111#', $format );
            $format = str_replace( 'M', '#222222#', $format );

            // Same for the Days
            $format = str_replace( 'D', '#333333#', $format );
            $format = str_replace( 'l', '#444444#', $format );

            $date_out = date( $format, intval( $params['field_value'] ) );

            $month = date( 'm', intval( $params['field_value'] ) );
            $month_full = $wp_locale->get_month( $month );
            $date_out = str_replace( '#111111#', $month_full, $date_out );
            $month_short = $wp_locale->get_month_abbrev( $month_full );
            $date_out = str_replace( '#222222#', $month_short, $date_out );

            $day = date( 'w', intval( $params['field_value'] ) );
            $day_full = $wp_locale->get_weekday( $day );
            $date_out = str_replace( '#444444#', $day_full, $date_out );
            $day_short = $wp_locale->get_weekday_abbrev( $day_full );
            $date_out = str_replace( '#333333#', $day_short, $date_out );

            $output = $date_out;
            break;
    }

    return $output;
}

/**
 * TinyMCE editor form.
 */
function wpcf_fields_date_editor_callback() {
    $last_settings = wpcf_admin_fields_get_field_last_settings( $_GET['field_id'] );
    wp_enqueue_script( 'jquery' );
    $form = array();
    $form['#form']['callback'] = 'wpcf_fields_date_editor_form_submit';
    $form['style'] = array(
        '#type' => 'radios',
        '#name' => 'wpcf[style]',
        '#options' => array(
            __( 'Show as calendar', 'wpcf' ) => 'calendar',
            __( 'Show as text', 'wpcf' ) => 'text',
        ),
        '#default_value' => isset( $last_settings['style'] ) ? $last_settings['style'] : 'text',
        '#after' => '<br />',
    );
    $date_formats = apply_filters( 'date_formats',
            array(
        __( 'F j, Y' ),
        'Y/m/d',
        'm/d/Y',
        'd/m/Y',
            )
    );
    $options = array();
    foreach ( $date_formats as $format ) {
        $title = date( $format, time() );
        $field['#title'] = $title;
        $field['#value'] = $format;
        $options[] = $field;
    }
    $custom_format = isset( $last_settings['format-custom'] ) ? $last_settings['format-custom'] : get_option( 'date_format' );
    $options[] = array(
        '#title' => __( 'Custom', 'wpcf' ),
        '#value' => 'custom',
        '#suffix' => wpcf_form_simple( array('custom' => array(
                '#name' => 'wpcf[format-custom]',
                '#type' => 'textfield',
                '#value' => $custom_format,
                '#suffix' => '&nbsp;' . date( $custom_format, time() ),
                '#inline' => true,
                ))
        ),
    );
    $form['toggle-open'] = array(
        '#type' => 'markup',
        '#markup' => '<div id="wpcf-toggle" style="display:none;">',
    );
    $form['format'] = array(
        '#type' => 'radios',
        '#name' => 'wpcf[format]',
        '#options' => $options,
        '#default_value' => isset( $last_settings['format'] ) ? $last_settings['format'] : get_option( 'date_format' ),
        '#after' => '<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">'
        . __( 'Documentation on date and time formatting', 'wpcf' ) . '</a>',
    );
    $form['toggle-close'] = array(
        '#type' => 'markup',
        '#markup' => '</div>',
    );
    $form['field_id'] = array(
        '#type' => 'hidden',
        '#name' => 'wpcf[field_id]',
        '#value' => $_GET['field_id'],
    );
    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => __( 'Insert date', 'wpcf' ),
        '#attributes' => array('class' => 'button-primary'),
    );
    $f = wpcf_form( 'wpcf-fields-date-editor', $form );
    add_action( 'admin_head_wpcf_ajax', 'wpcf_fields_date_editor_form_script' );
    wpcf_admin_ajax_head( __( 'Insert date', 'wpcf' ) );
    echo '<form id="wpcf-form" method="post" action="">';
    echo $f->renderForm();
    echo '</form>';
    wpcf_admin_ajax_footer();
}

/**
 * Inserts shortcode in editor.
 * 
 * @return type 
 */
function wpcf_fields_date_editor_form_submit() {
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
    if ( !isset( $_POST['wpcf']['field_id'] ) ) {
        return false;
    }
    $field = wpcf_admin_fields_get_field( $_POST['wpcf']['field_id'] );
    /* Check if usermeta field */
    if ( empty( $field ) ) {
        $field = wpcf_admin_fields_get_field( $_POST['wpcf']['field_id'], false,
                false, false, 'wpcf-usermeta' );
        $types_attr = 'usermeta';
    }
    /* End if */
    if ( empty( $field ) ) {
        return false;
    }
    $add = ' ';
    $style = isset( $_POST['wpcf']['style'] ) ? $_POST['wpcf']['style'] : 'text';
    $add .= 'style="' . $style . '"';
    $format = '';
    if ( $style == 'text' ) {
        if ( $_POST['wpcf']['format'] == 'custom' ) {
            $format = $_POST['wpcf']['format-custom'];
        } else {
            $format = $_POST['wpcf']['format'];
        }
        if ( empty( $format ) ) {
            $format = get_option( 'date_format' );
        }
        $add .= ' format="' . $format . '"';
    }
    if ( $types_attr == 'usermeta' ) {
        $shortcode = wpcf_usermeta_get_shortcode( $field, $add );
    } else {
        $shortcode = wpcf_fields_get_shortcode( $field, $add );
    }
    wpcf_admin_fields_save_field_last_settings( $_POST['wpcf']['field_id'],
            array(
        'style' => $style,
        'format' => $_POST['wpcf']['format'],
        'format-custom' => $_POST['wpcf']['format-custom'],
            )
    );
    echo editor_admin_popup_insert_shortcode_js( $shortcode );
    die();
}