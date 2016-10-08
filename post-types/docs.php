<?php

add_action( 'init', 'ninja_docs_cpt' );
function ninja_docs_cpt()
{
  $labels = array(
    'name' => _x('Documentation', 'post type general name'),
    'singular_name' => _x('Documentation', 'post type singular name'),
    'add_new' => _x('Add New', 'document'),
    'add_new_item' => __('Add New Document'),
    'edit_item' => __('Edit document'),
    'new_item' => __('New document'),
    'view_item' => __('View document'),
    'search_items' => __('Search documents'),
    'not_found' =>  __('No documents found'),
    'not_found_in_trash' => __('No documents found in Trash'),
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    '_builtin' => false, // It's a custom post type, not built in!
    'query_var' => true,
    'show_in_menu' => true,
    'capability_type' => 'post',
    'has_archive' => false,
    'hierarchical' => true,
    'menu_events' => null,
    'rewrite' => array( "slug" => apply_filters( 'nd_doc_slug', 'docs' ) ), // Permalinks format
    'menu_icon' => 'dashicons-book-alt',
    'taxonomies' => array('doc_tags, doc_category'),
    'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'genesis-cpt-archives-settings', 'genesis-layouts' ),
  );
  register_post_type('ninja_docs_cpt',$args);
}

//add filter to insure the text Document, or document, is displayed when user updates a document
add_filter('post_updated_messages', 'ninja_docs_updated_messages');
function ninja_docs_updated_messages( $messages ) {
    global $post;
  $messages['forms_docs'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Document updated. <a href="%s">View Document</a>'), esc_url( get_permalink($post->ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Document updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Document restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Document published. <a href="%s">View Document</a>'), esc_url( get_permalink($post->ID) ) ),
    7 => __('Document saved.'),
    8 => sprintf( __('Document submitted. <a target="_blank" href="%s">Preview Document</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
    9 => sprintf( __('Document scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Document</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post->ID) ) ),
    10 => sprintf( __('Document draft updated. <a target="_blank" href="%s">Preview Document</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
  );

  return $messages;
}

add_action( 'init', 'ninja_docs_taxonomies', 0 );

function ninja_docs_taxonomies()
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name' => _x( 'Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Categories' ),
    'all_items' => __( 'All Categories' ),
    'parent_item' => __( 'Parent Category' ),
    'parent_item_colon' => __( 'Parent Title:' ),
    'edit_item' => __( 'Edit Category' ),
    'update_item' => __( 'Update Category' ),
    'add_new_item' => __( 'Add New Category' ),
    'new_item_name' => __( 'New Category Name' ),
  );

  register_taxonomy( 'doc_category', array('ninja_docs_cpt' ), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'documentation/category' ),
  ));

  $labels = array(
    'name' => _x( 'Tags', 'taxonomy general name' ),
    'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Tags' ),
    'all_items' => __( 'All Tags' ),
    'parent_item' => __( 'Parent Tags' ),
    'parent_item_colon' => __( 'Parent Title:' ),
    'edit_item' => __( 'Edit Tag' ),
    'update_item' => __( 'Update Tag' ),
    'add_new_item' => __( 'Add New Tag' ),
    'new_item_name' => __( 'New Tag Name' ),
  );

  register_taxonomy( 'doc_tags', array('ninja_docs_cpt' ), array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'documentation/tag' ),
  ));

}

function ninja_docs_filters() {
    global $typenow;

    // an array of all the taxonomyies you want to display. Use the taxonomy name or slug
    $taxonomies = array( 'doc_category', 'doc_tags' );

    // must set this to the post type you want the filter(s) displayed on
    if ( $typenow == 'ninja_docs_cpt' ){

        foreach ( $taxonomies as $tax_slug ) {
            $tax_obj = get_taxonomy( $tax_slug );
            //print_r($tax_obj);
            $tax_name = $tax_obj->labels->name;
            $terms = get_terms( $tax_slug );
            //print_r($terms);
            if ( count( $terms ) > 0 ) {
                echo '<select name=' . $tax_slug . ' id=' . $tax_slug . ' class="postform">';
                echo '<option value="">Show All ' . $tax_name . '</option>';
                foreach ( $terms as $term ) {
                    echo '<option value='.$term->slug.' ' . selected( $term->slug, $_GET[$tax_slug] ) . '>' . $term->name .' (' . $term->count .')</option>';
                }
                echo "</select>";
            }
        }
    }
}
add_action( 'restrict_manage_posts', 'ninja_docs_filters' );

add_filter( 'manage_edit-ninja_docs_cpt_columns', 'ninja_docs_columns' );

function ninja_docs_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __( 'Document' ),
        'doc_category' => __( 'Categories' ),
        'doc_tags' => __( 'Tags' ),
        'date' => __( 'Date' )
    );

    return $columns;
}


add_action( 'manage_ninja_docs_cpt_posts_custom_column', 'ninja_docs_manage_columns', 10, 2 );

function ninja_docs_manage_columns( $column ) {
    global $post;

    switch( $column ) {

        /* If displaying the 'duration' column. */
        case 'doc_category' :
            /* Get the post terms. */
            $terms = get_the_terms( $post->ID, 'doc_category' );
            //print_r($terms);

            /* If no duration is found, output a default message. */
            if ( empty( $terms ) ) {
                echo __( 'None' );
            } else {

                $out = array();

                /* Loop through each term, linking to the 'edit posts' page for the specific term. */
                foreach ( $terms as $term ) {
                    $out[] = sprintf( '<a href="%s">%s</a>',
                        esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'doc_category' => $term->slug ), 'edit.php' ) ),
                        esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'doc_category', 'display' ) )
                    );
                }

                /* Join the terms, separating them with a comma. */
                echo join( ', ', $out );
            }

            break;

        case 'doc_tags' :

            /* Get the post terms. */
            $terms = get_the_terms( $post->ID, 'doc_tags' );
            //print_r($terms);

            /* If no duration is found, output a default message. */
            if ( empty( $terms ) ) {
                echo __( 'None' );
            } else {

                $out = array();

                /* Loop through each term, linking to the 'edit posts' page for the specific term. */
                foreach ( $terms as $term ) {
                    $out[] = sprintf( '<a href="%s">%s</a>',
                        esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'doc_tags' => $term->slug ), 'edit.php' ) ),
                        esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'doc_tags', 'display' ) )
                    );
                }

                /* Join the terms, separating them with a comma. */
                echo join( ', ', $out );
            }
            break;

        /* Just break out of the switch statement for everything else. */
        default :
            break;
    }
}

//add_post_type_support( 'post_type_id_here', 'genesis-layouts' );
