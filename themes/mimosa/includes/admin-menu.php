<?php
add_action( 'admin_menu', 'my_admin_menu' );
function my_admin_menu() {
    add_options_page( 'My Plugin', 'My Plugin', 'manage_options', 'mimosa-plugin', 'my_options_page' );
}

add_action( 'admin_init', 'my_admin_init' );
function my_admin_init() {
    register_setting( 'my-settings-group', 'my-setting' );
    add_settings_section( 'section-one', 'Section One', 'section_one_callback', 'mimosa-plugin' );
    add_settings_field( 'field-one', 'Field One', 'field_one_callback', 'mimosa-plugin', 'section-one' );
}

function section_one_callback() {
    echo 'Some help text goes here.';
}

function field_one_callback() {
    $setting = esc_attr( get_option( 'my-setting' ) );
    echo "<input type='text' name='my-setting' value='$setting' />";
}

function my_options_page() {
    ?>
    <div class="wrap">
        <h2>My Plugin Options</h2>
        <form action="options.php" method="POST">
            <?php settings_fields( 'my-settings-group' ); ?>
            <?php do_settings_sections( 'my-plugin' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

?>