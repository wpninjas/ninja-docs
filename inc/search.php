<?php

function ninja_docs_get_search_forms( $query ) {
    if ( ! class_exists( 'SearchWP' ) )
        return;

    $searchvar = isset( $_GET['searchvar'] ) ? sanitize_text_field( $_GET['searchvar'] ) : '';
    return '<div class="search-box">
        <form role="search" method="get" class="search-form" action="' . get_permalink( 343937 ) . '">
            <label>
                <input type="search" class="search-field" placeholder="Search our documentation..." value="' . esc_attr( $searchvar ) . '" name="searchvar"  data-swplive="true" data-swpengine="swp_docs" data-swpconfig="swp_docs" title="Search for:">
            </label>
            <input type="submit" class="search-submit" value="Search">
        </form>
    </div>';
}

function my_searchwp_live_search_configs( $configs ) {
    // override some defaults
    $configs['swp_docs'] = array(
        'engine' => 'swp_docs',                      // search engine to use (if SearchWP is available)
        'input' => array(
            'delay'     => 500,                 // wait 500ms before triggering a search
            'min_chars' => 3,                   // wait for at least 3 characters before triggering a search
        ),
        'results' => array(
            'position'  => 'bottom',            // where to position the results (bottom|top)
            'width'     => 'auto',              // whether the width should automatically match the input (auto|css)
            'offset'    => array(
                'x' => 0,                   // x offset (in pixels)
                'y' => 5                    // y offset (in pixels)
            ),
        ),
        'spinner' => array(                         // powered by http://fgnass.github.io/spin.js/
            'lines'         => 10,              // number of lines in the spinner
            'length'        => 8,               // length of each line
            'width'         => 4,               // line thickness
            'radius'        => 8,               // radius of inner circle
            'corners'       => 1,               // corner roundness (0..1)
            'rotate'        => 0,               // rotation offset
            'direction'     => 1,               // 1: clockwise, -1: counterclockwise
            'color'         => '#000',          // #rgb or #rrggbb or array of colors
            'speed'         => 1,               // rounds per second
            'trail'         => 60,              // afterglow percentage
            'shadow'        => false,           // whether to render a shadow
            'hwaccel'       => false,           // whether to use hardware acceleration
            'className'     => 'spinner',       // CSS class assigned to spinner
            'zIndex'        => 2000000000,      // z-index of spinner
            'top'           => '50%',           // top position (relative to parent)
            'left'          => '50%',           // left position (relative to parent)
        ),
    );

    return $configs;
}
add_filter( 'searchwp_live_search_configs', 'my_searchwp_live_search_configs' );
