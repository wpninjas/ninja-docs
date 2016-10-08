<?php

function ninja_docs_edit_suggestions() {
    if ( current_user_can( 'manage_options' ) && is_page( 'docs-preview' ) ) {
        $args = array(
            'post_type' => 'ninja_docs_cpt',
            'posts_per_page' => -1,  //show all posts
            'meta_query' => array(
                array(
                    'key' => 'doc_user_suggestion',
                    'compare' => 'EXISTS',
                )
            )

        );
        $posts = new WP_Query($args);
        if( $posts->have_posts() ):
            echo '<h2>Documents with Suggested Edits</h2>';
            echo '<ul class="edit-suggestions">';
            while( $posts->have_posts() ) : $posts->the_post();
                echo '<li>';
                    //echo '<a href="' . get_permalink() . '">';
                        echo '<h3>' . get_the_title() . ' - <a href="' . get_edit_post_link() . '">Edit</a></h3>';
                    //echo '</a>';
                    echo '<div class="one-fourth first"><h4>Suggestions:</h4></div>';
                    $suggestions = get_post_meta( get_the_ID(), 'doc_user_suggestion' );
                    echo '<div class="three-fourths">';
                        echo '<ul>';
                        foreach ($suggestions as $suggestion) {
                            echo '<li>' . $suggestion . '</li>';
                        }
                        echo '</ul>';
                    echo '</div>';
                echo '</li>';
            endwhile;
            echo '</ul>';
        endif;
    }
}
add_action( 'theme_after_content', 'ninja_docs_edit_suggestions' );
