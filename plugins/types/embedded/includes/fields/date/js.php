<?php

/**
 * Renders inline JS.
 */
function wpcf_fields_date_meta_box_js_inline() {

    $date_format = wpcf_get_date_format();
    $date_format = _wpcf_date_convert_wp_to_js( $date_format );

    $date_format_note = '<span style="margin-left:10px"><i>' . esc_js( sprintf( __( 'Input format: %s',
                                    'wpcf' ), wpcf_get_date_format_text() ) ) . '</i></span>';

    ?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function(){
            wpcfFieldsDateInit('');
        });
        function wpcfFieldsDateInit(div) {
            if (jQuery.isFunction(jQuery.fn.datepicker)) {
                jQuery(div+' .wpcf-datepicker').each(function(index) {
                    if (!jQuery(this).is(':disabled') && !jQuery(this).hasClass('hasDatepicker')) {
                        jQuery(this).datepicker({
                            showOn: "button",
                            buttonImage: "<?php echo WPCF_EMBEDDED_RES_RELPATH; ?>/images/calendar.gif",
                            buttonImageOnly: true,
                            buttonText: "<?php
    _e( 'Select date', 'wpcf' );

    ?>",
                            dateFormat: "<?php echo $date_format; ?>",
                            altFormat: "<?php echo $date_format; ?>",
                            onSelect: function(dateText, inst) {
                                jQuery(this).trigger('wpcfDateBlur');
                            }
                        });
                        jQuery(this).next().after('<?php echo $date_format_note; ?>');
                    }
                });
            }
        }
        function wpcfFieldsDateEditorCallback(field_id) {
            var url = "<?php echo admin_url( 'admin-ajax.php' ); ?>?action=wpcf_ajax&wpcf_action=editor_insert_date&_wpnonce=<?php echo wp_create_nonce( 'fields_insert' ); ?>&field_id="+field_id+"&keepThis=true&TB_iframe=true&width=400&height=400";
            tb_show("<?php
    _e( 'Insert date', 'wpcf' );

    ?>", url);
        }
        //]]>
    </script>
    <?php
}

/**
 * AJAX window JS.
 */
function wpcf_fields_date_editor_form_script() {

    ?>
    <script type="text/javascript">
        // <![CDATA[
        jQuery(document).ready(function(){
            jQuery('input[name|="wpcf[style]"]').change(function(){
                if (jQuery(this).val() == 'text') {
                    jQuery('#wpcf-toggle').slideDown();
                } else {
                    jQuery('#wpcf-toggle').slideUp();
                }
            });
            if (jQuery('input[name="wpcf[style]"]:checked').val() == 'text') {
                jQuery('#wpcf-toggle').show();
            }
        });
        // ]]>
    </script>
    <?php
}