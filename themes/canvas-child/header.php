<?php
/**
 * Header Template
 *
 * Here we setup all logic and XHTML that is required for the header section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
 
 // Setup the tag to be used for the header area (`h1` on the front page and `span` on all others).
 $heading_tag = 'span';
 if ( is_home() OR is_front_page() ) { $heading_tag = 'h1'; }
 
 // Get our website's name, description and URL. We use them several times below so lets get them once.
 $site_title = get_bloginfo( 'name' );
 $site_url = home_url( '/' );
 $site_description = get_bloginfo( 'description' );
 
 global $woo_options;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
<title><?php woo_title(); ?></title>
<?php woo_meta(); ?>
<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>" />
<?php wp_head(); ?>
<?php woo_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php woo_top(); ?>
<div id="wrapper">        
  <?php woo_header_before(); ?>
    
  <div id="header" class="col-full">
    
    <?php woo_header_inside(); ?>
       
    <div id="logo">
    <?php
      // Website heading/logo and description text.
      if ( isset( $woo_options['woo_logo'] ) && ( '' != $woo_options['woo_logo'] ) ) {
        $logo_url = $woo_options['woo_logo'];
        if ( is_ssl() ) $logo_url = str_replace( 'http://', 'https://', $logo_url );

        echo '<a href="' . esc_url( $site_url ) . '" title="' . esc_attr( $site_description ) . '"><img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $site_title ) . '" /></a>' . "\n";
      } // End IF Statement
      
      echo '<' . $heading_tag . ' class="site-title"><a href="' . esc_url( $site_url ) . '">' . $site_title . '</a></' . $heading_tag . '>' . "\n";
      if ( $site_description ) { echo '<span class="site-description">' . $site_description . '</span>' . "\n"; }
    ?>
    </div><!-- /#logo -->
        
      <h3 class="nav-toggle icon"><a href="#navigation"><?php _e( 'Navigation', 'woothemes' ); ?></a></h3>
        
    <?php if ( isset( $woo_options['woo_ad_top'] ) && ( 'true' == $woo_options['woo_ad_top'] ) ) { ?>
        <div id="topad">
        
    <?php if ( ( isset( $woo_options['woo_ad_top_adsense'] ) ) && ( '' != $woo_options['woo_ad_top_adsense'] ) ) { 
            echo stripslashes( get_option('woo_ad_top_adsense') );             
        } else {
          $top_ad_image = $woo_options['woo_ad_top_image'];
          if ( is_ssl() ) $top_ad_image = str_replace( 'http://', 'https://', $top_ad_image );
        ?>
            <a href="<?php echo esc_url( get_option( 'woo_ad_top_url' ) ); ?>"><img src="<?php echo esc_url( $top_ad_image ); ?>" alt="" /></a>
        <?php } ?>        
            
        </div><!-- /#topad -->
        <?php } ?>
       
  </div><!-- /#header -->
  <?php woo_header_after(); ?>