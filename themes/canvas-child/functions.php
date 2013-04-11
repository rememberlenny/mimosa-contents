<?php

// Important Canvas links
// http://www.woothemes.com/tutorials/canvas-using-hooks-and-filters/
// http://www.woothemes.com/woocodex/canvas-hooked-filtered-functions/

// Move the navigation to the right of the Logo. 
// http://www.woothemes.com/tutorials/move-the-logo-outside-of-the-header-in-canvas/
// Note: There is a complimentary CSS declaration that floats right and widths auto.

add_action( 'init', 'woo_custom_move_navigation', 10 );
 
function woo_custom_move_navigation () {
    // Remove main nav from the woo_header_after hook
    remove_action( 'woo_header_after','woo_nav', 10 );
    // Add main nav to the woo_header_inside hook
    add_action( 'woo_header_inside','woo_nav', 10 );
} // End woo_custom_move_navigation()

//Add typekit asynchronous javascript

function lkbg_typekit () {
	?>
	<script type="text/javascript">
	  (function() {
	    var config = {
	      kitId: 'ewy4ijp',
	      scriptTimeout: 3000
	    };
	    var h=document.getElementsByTagName("html")[0];h.className+=" wf-loading";var t=setTimeout(function(){h.className=h.className.replace(/(\s|^)wf-loading(\s|$)/g," ");h.className+=" wf-inactive"},config.scriptTimeout);var tk=document.createElement("script"),d=false;tk.src='//use.typekit.net/'+config.kitId+'.js';tk.type="text/javascript";tk.async="true";tk.onload=tk.onreadystatechange=function(){var a=this.readyState;if(d||a&&a!="complete"&&a!="loaded")return;d=true;clearTimeout(t);try{Typekit.load(config)}catch(b){}};var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(tk,s)
	  })();
	</script>
	<?php
}
add_action( 'wp_head', 'lkbg_typekit', 10);


// Remove Google Fonts
function woo_google_webfonts() { 
// do nothing 
}


// Add slider to homepage
// http://www.woothemes.com/tutorials/add-the-business-slider-to-the-default-wordpress-homepage/?codekitCB=376980235.250927 
// Display the "Business" slider above the default WordPress homepage.
add_action( 'get_header', 'woo_custom_load_biz_slider', 10 );
 
function woo_custom_load_biz_slider () {
    if ( is_front_page() && ! is_paged() ) {
        add_action( 'woo_main_before_home', 'woo_slider_biz', 10 );
        add_action( 'woo_main_before_home', 'woo_custom_reset_biz_query', 11 );
        add_action( 'woo_load_slider_js', '__return_true', 10 );
        add_filter( 'body_class', 'woo_custom_add_business_bodyclass', 10 );
    }
} // End woo_custom_load_biz_slider()
 
function woo_custom_add_business_bodyclass ( $classes ) {
    if ( is_home() ) {
        $classes[] = 'business';
    }
    return $classes;
} // End woo_custom_add_biz_bodyclass()
 
function woo_custom_reset_biz_query () {
    wp_reset_query();
} // End woo_custom_reset_biz_query()



add_filter( 'woo_portfolio_gallery_exclude', 'woo_custom_portfolio_gallery_slugs', 10 );
 
function woo_custom_portfolio_gallery_slugs ( $slugs ) {
    $slugs = 'design';
    return $slugs;
} // End woo_custom_portfolio_gallery_slugs()



/*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Fabrication Item (Portfolio Component) */
/*-----------------------------------------------------------------------------------*/


    function woo_add_fabrication() {
    
        global $woo_options;
    
        // Sanity check.
    
        // "Fabrication Item" Custom Post Type
        $labels = array(
            'name' => _x( 'Fabrication', 'post type general name', 'woothemes' ),
            'singular_name' => _x( 'Fabrication Item', 'post type singular name', 'woothemes' ),
            'add_new' => _x( 'Add New', 'slide', 'woothemes' ),
            'add_new_item' => __( 'Add New Fabrication Item', 'woothemes' ),
            'edit_item' => __( 'Edit Fabrication Item', 'woothemes' ),
            'new_item' => __( 'New Fabrication Item', 'woothemes' ),
            'view_item' => __( 'View Fabrication Item', 'woothemes' ),
            'search_items' => __( 'Search Fabrication Items', 'woothemes' ),
            'not_found' =>  __( 'No Fabrication items found', 'woothemes' ),
            'not_found_in_trash' => __( 'No Fabrication items found in Trash', 'woothemes' ), 
            'parent_item_colon' => ''
        );
        
        $fabricationitems_rewrite = 'fabrication-items'; 
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true, 
            'query_var' => true,
            'rewrite' => array( 'slug' => $fabricationitems_rewrite ),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_icon' => get_template_directory_uri() .'/includes/images/portfolio.png',
            'menu_position' => null, 
            'has_archive' => true, 
            'taxonomies' => array( 'fabrication-gallery' ), 
            'supports' => array( 'title','editor','thumbnail' )
        );

        
        register_post_type( 'fabrication', $args );
        
        // "Fabrication Galleries" Custom Taxonomy
        $labels = array(
            'name' => _x( 'Fabrication Galleries', 'taxonomy general name', 'woothemes' ),
            'singular_name' => _x( 'Fabrication Gallery', 'taxonomy singular name','woothemes' ),
            'search_items' =>  __( 'Search Fabrication Galleries', 'woothemes' ),
            'all_items' => __( 'All Fabrication Galleries', 'woothemes' ),
            'parent_item' => __( 'Parent Fabrication Gallery', 'woothemes' ),
            'parent_item_colon' => __( 'Parent Fabrication Gallery:', 'woothemes' ),
            'edit_item' => __( 'Edit Fabrication Gallery', 'woothemes' ), 
            'update_item' => __( 'Update Fabrication Gallery', 'woothemes' ),
            'add_new_item' => __( 'Add New Fabrication Gallery', 'woothemes' ),
            'new_item_name' => __( 'New Fabrication Gallery Name', 'woothemes' ),
            'menu_name' => __( 'Fabrication Galleries', 'woothemes' )
        );  
        
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'fabrication-gallery' )
        );
        
        register_taxonomy( 'fabrication-gallery', array( 'fabrication' ), $args );
    }
    
    add_action( 'init', 'woo_add_fabrication' );


/*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Design Item (Design Component) */
/*-----------------------------------------------------------------------------------*/


    function woo_add_portfolio() {
    
        global $woo_options;

    
        // "Design Item" Custom Post Type
        $labels = array(
            'name' => _x( 'Design', 'post type general name', 'woothemes' ),
            'singular_name' => _x( 'Design Item', 'post type singular name', 'woothemes' ),
            'add_new' => _x( 'Add New', 'slide', 'woothemes' ),
            'add_new_item' => __( 'Add New Design Item', 'woothemes' ),
            'edit_item' => __( 'Edit Design Item', 'woothemes' ),
            'new_item' => __( 'New Design Item', 'woothemes' ),
            'view_item' => __( 'View Design Item', 'woothemes' ),
            'search_items' => __( 'Search Design Items', 'woothemes' ),
            'not_found' =>  __( 'No Design items found', 'woothemes' ),
            'not_found_in_trash' => __( 'No Design items found in Trash', 'woothemes' ), 
            'parent_item_colon' => ''
        );
        

        $designitems_rewrite = 'design-items'; 
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true, 
            'query_var' => true,
            'rewrite' => array( 'slug' => $designitems_rewrite ),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_icon' => get_template_directory_uri() .'/includes/images/portfolio.png',
            'menu_position' => null, 
            'has_archive' => true, 
            'taxonomies' => array( 'design-gallery' ), 
            'supports' => array( 'title','editor','thumbnail' )
        );
        
        register_post_type( 'design', $args );
        
        // "Design Galleries" Custom Taxonomy
        $labels = array(
            'name' => _x( 'Design Galleries', 'taxonomy general name', 'woothemes' ),
            'singular_name' => _x( 'Design Gallery', 'taxonomy singular name','woothemes' ),
            'search_items' =>  __( 'Search Design Galleries', 'woothemes' ),
            'all_items' => __( 'All Design Galleries', 'woothemes' ),
            'parent_item' => __( 'Parent Design Gallery', 'woothemes' ),
            'parent_item_colon' => __( 'Parent Design Gallery:', 'woothemes' ),
            'edit_item' => __( 'Edit Design Gallery', 'woothemes' ), 
            'update_item' => __( 'Update Design Gallery', 'woothemes' ),
            'add_new_item' => __( 'Add New Design Gallery', 'woothemes' ),
            'new_item_name' => __( 'New Design Gallery Name', 'woothemes' ),
            'menu_name' => __( 'Design Galleries', 'woothemes' )
        );  
        
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'design-gallery' )
        );
        
        register_taxonomy( 'design-gallery', array( 'design' ), $args );
    }
    
    add_action( 'init', 'woo_add_design' );




?>