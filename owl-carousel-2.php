<?php
/*
    Plugin Name: Owl Carousel 2
    Description: The Owl Carousel v2 Slideshow into WordPress
    Author: Marcus Tisäter
    Version: 1.0
*/

function owl_init() {
    add_image_size('owl_widget', 180, 100, true);
    add_image_size('owl_function', 600, 280, true);
    add_theme_support( 'post-thumbnails');

    $args = array(
        'public' => true,
        'label' => 'Owl Carousel',
        'taxonomies' => array( 'category'),
        'menu_icon' => 'dashicons-images-alt2',
        'supports' => array(
            'title',
            'excerpt',
            'thumbnail'
        )
    );
    register_post_type('owl_images', $args);
}

add_action('init', 'owl_init');

function owl_scripts() {

  wp_enqueue_style('owl-style', plugins_url('assets/owl.carousel.css', __FILE__));
  wp_enqueue_style('owl-style-custom', plugins_url('theme.css', __FILE__));

  wp_deregister_script('jquery');


  wp_register_script('jquery', plugins_url('assets/jquery.js', __FILE__), false, '1.11.3', true);

  wp_enqueue_script('jquery', plugins_url('assets/jquery.js', __FILE__), array('jquery'), '', true);
  wp_enqueue_script('owl-carousel-script', plugins_url('assets/owl.carousel.min.js' , __FILE__) , array('jquery'), '', true);
  wp_enqueue_script('owl-carousel-script-custom', plugins_url('theme.js' , __FILE__) , array('jquery'), '', true);
}

add_action( 'wp_enqueue_scripts', 'owl_scripts');




function owl_function($atts, $type='owl_function') {
    $atts = shortcode_atts(
  		array(
        'category' => 'default category',
  		), $atts, 'owl' );

    $args = array(
        'category_name'=>$atts['category'],
        'post_type' => 'owl_images',
        'posts_per_page' => 5,
    );

    $result = '<div class="slideshow">';
    $result = '<div class="owl-carousel owl-theme">';
    //the loop
    $loop = new WP_Query($args);
    while ($loop->have_posts()) {
        $loop->the_post();

        $the_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $type);

        if (get_the_title() OR (get_the_excerpt())){
          $result .='<div class="item"> <img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/> <div class="owl-title"> <h3>' .get_the_title(). '</h3> <p class="owl-excerpt">' .get_the_excerpt(). '</p> </div> </div>';
        }

        else {
          $result .='<div class="item"> <img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/> </div>';
        }
    }
    $result .='</div>';
    return $result;
}

add_shortcode('owl', 'owl_function');


add_action( 'admin_head', 'owl_add_tinymce' );
function owl_add_tinymce() {
    global $typenow;
    // Only on Post Type: post and page
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return ;

    add_filter( 'mce_external_plugins', 'owl_add_tinymce_plugin' );
    add_filter( 'mce_buttons', 'owl_add_tinymce_button' );
}

function owl_add_tinymce_plugin( $plugin_array ) {

    $plugin_array['owl_test'] = plugins_url( 'assets/shortcode.js', __FILE__ );
    var_dump( $plugin_array );
    return $plugin_array;
}

// Add the button key for address via JS
function owl_add_tinymce_button( $buttons ) {

    array_push( $buttons, 'owl_test_button_key' );
    var_dump( $buttons );
    return $buttons;
}

/* Widget */

function owl_widgets_init() {
    register_widget('owl_Widget');
}

add_action('widgets_init', 'owl_widgets_init');


class owl_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('owl_Widget', 'Owl Carousel Slideshow', array('description' => __('Owl Carousel 2 Slideshow Widget', 'owl-carousel-2')));
    }
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        }
        else {
            $title = __('Widget Slideshow', 'owl-carousel-2');
        }
        ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    public function widget($args, $instance) {
        extract($args);
        // the title
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;
        echo owl_function('owl_widget');
        echo $after_widget;
    }
}



?>
