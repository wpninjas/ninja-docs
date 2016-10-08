<?php

function ninja_docs_get_arhcive() {
    $content = '';
    if( current_user_can( 'manage_options' ) ) {
        //$content .= ninja_docs_get_search_forms( $query = '' );
    }
    $post_type = 'ninja_docs_cpt';

    $page_id = get_the_ID();

    $terms = get_terms( 'doc_category' );
    usort($terms, 'ninja_docs_sort_objects_by_term_id');

    $content .= '<ul class="ninja_docs_list">';
    foreach( $terms as $term ) :
        $content .= '<li class="doc-category"><h2><i class="fa fa-folder"></i>' . $term->name . '</h2>';

            $args = array(
                'post_type' => $post_type,
                'posts_per_page' => -1,  //show all posts
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'doc_category',
                        'field' => 'slug',
                        'terms' => $term->slug,
                    )
                )

            );
            $posts = new WP_Query($args);

            if( $posts->have_posts() ):
                $content .= '<ul>';
                while( $posts->have_posts() ) : $posts->the_post();
                    $menu_id = get_the_ID();
                    if ( $page_id == $menu_id ) {
                        $state = ' class="current"';
                    } else {
                        $state = '';
                    }
                    $content .= '<li' . $state . '>';
                        $content .= '<i class="fa fa-file"></i><a href="' . get_permalink() . '">';
                            $content .= get_the_title();
                        $content .= '</a>';
                    $content .= '</li>';
                endwhile;
                $content .= '</ul>';
            endif;
            wp_reset_postdata();
        $content .= '</li>';
    endforeach;
    $content .= '</ul>';
    return $content;
}

add_shortcode( 'docs_list', 'ninja_docs_get_arhcive' );

function ninja_docs_build_archive_page() {
    if ( is_page( 'documentation' ) ) {
        echo ninja_docs_get_arhcive();
    }
}

add_action( 'wp_enqueue_scripts', 'ninja_docs_archive_scripts_masonry' );
function ninja_docs_archive_scripts_masonry() {
    wp_enqueue_script('masonry');
    //wp_enqueue_style('masonry’, get_template_directory_uri().'/css/’);
}

function ninja_docs_masonry_init() {
  if ( is_page( 'documentation' ) ) { ?>
    <script>
      jQuery( document ).ready( function() {
        //set the container that Masonry will be inside of in a var
        var container = document.querySelector('.ninja_docs_list');
        //create empty var msnry
        var msnry;
        // initialize Masonry after all images have loaded
        imagesLoaded( container, function() {
          msnry = new Masonry( container, {
              itemSelector: '.doc-category'
          });
        });
      });
    </script>
  <?php }
}
add_action( 'wp_footer', 'ninja_docs_masonry_init' );

function ninja_docs_add_form() {
    if ( is_single() && 'ninja_docs_cpt' == get_post_type() && function_exists( 'ninja_forms_display_form' ) ) {
        ninja_forms_display_form( 149 );
    }
}
add_action( 'theme_after_content', 'ninja_docs_add_form' );


function ninja_docs_sort_objects_by_term_id($a, $b) {
    if($a->term_id == $b->term_id){ return 0 ; }
    return ($a->term_id < $b->term_id) ? -1 : 1;
}
