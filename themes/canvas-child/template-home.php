<?php
/**
 * Template Name: Home
 *
 * The magazine page template displays your posts with a "magazine"-style
 * content slider at the top and a grid of posts below it. 
 *
 * @package WooFramework
 * @subpackage Template
 */

 global $woo_options, $post; 
 get_header();

 if ( is_paged() ) $is_paged = true; else $is_paged = false;
 
 $page_template = woo_get_page_template();
?>

    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="col-full magazine">
    
    	<div id="main-sidebar-container">

            <!-- #main Starts -->
            <?php woo_main_before(); ?>
            <div id="main">
            	<?php woo_loop_before(); ?>   
             	<?php if ( $woo_options['woo_slider_magazine'] == 'true' && ! $is_paged ) { if ( get_option( 'woo_exclude' ) ) update_option( 'woo_exclude', '' ); woo_slider_magazine(); } ?>
             	<div class="fix"></div>
                    <div class="fabrication">  
                <h2>Fabrication</h2>  
                    <?php 
                    rewind_posts();
                    $mypost = array( 'post_type' => 'fabrication' );
                    $my_query = new WP_Query( $mypost ); ?>

                    <?php if ( have_posts() ) : ?>

                    <?php /* Start the Loop */ ?>
                    <?php while ( $my_query->have_posts()) :  $my_query->the_post(); ?>

                    <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
                        <a href="<?php the_permalink(); ?>">
                        <div class="large-4 column" style=""><?php the_post_thumbnail(); ?></div>
                        </a>
                    </div>
                    
                    <?php endwhile; ?>

                    <?php else : ?>
                    <?php get_template_part( 'content', 'none' ); ?>

                    <?php endif; // end have_posts() check 
                    wp_reset_query();?>
                    </div>
                    
                    <div class="fix"></div>
                    
                    <div class="design">
                        <h2>Design</h2> 
                        <?php 
                    rewind_posts();
                    $mypost = array( 'post_type' => 'design' );
                    $my_query = new WP_Query( $mypost ); ?>

                    <?php if ( have_posts() ) : ?>

                    <?php /* Start the Loop */ ?>
                    <?php while ( $my_query->have_posts()) :  $my_query->the_post(); ?>

                    <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
                        <a href="<?php the_permalink(); ?>">
                        <div class="large-4 column" style=""><?php the_post_thumbnail( 'small'); ?></div>
                        </a>
                    </div>
                    
                    <?php endwhile; ?>

                    <?php else : ?>
                    <?php get_template_part( 'content', 'none' ); ?>

                    <?php endif; // end have_posts() check 
                    wp_reset_query();?>
                    </div>
            </div><!-- /#main -->
            <?php woo_main_after(); ?>
    
            <?php // get_sidebar(); ?>
            
		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php woo_content_after(); ?>
    
		
<?php get_footer(); ?>