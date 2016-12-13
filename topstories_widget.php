<?php
/*
Plugin Name: SRTK Top Stories Widget
Plugin URI: http://github.com/srthk
Description: A simple widget plugin to display posts from a category on the sidebar with a minimalstic design (mostly for trending/top-stories posts)
Version: 1.0
Author: Sarthak Singhal
Author URI: http://github.com/srthk
Text Domain: topstoriestextdomain
License: GPLv2
 
Copyright 2016 Sarthak
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
 
class TopStories_Widget extends WP_Widget {
 
    public function __construct() {
     
        parent::__construct(
            'topstories_widget',
            __( 'SRTK Top Stories Widget', 'topstoriestextdomain' ),
            array(
                'classname'   => 'topstories_widget',
                'description' => __( 'A widget to display top stories based on category.', 'topstoriestextdomain' )
                )
        );
       
        load_plugin_textdomain( 'topstoriestextdomain', false, basename( dirname( __FILE__ ) ) . '/languages' );
       
    }
 
    /**  
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
      $rowArray = array();
      extract( $args );   
      $title = apply_filters( 'widget_title', $instance['title'] );
      $count = $instance['count'];
      $category = $instance['category'];
      echo $before_widget;
      
      if ( $title ) {
            echo $before_title . $title . $after_title;
      }
      $args = array('category_name' => $category, 'numberposts' => $count);
      $posts = get_posts($args);
      foreach ($posts as $post) {
         $image = "";
         $post_title = $post->post_title;
         $post_url = get_permalink($post->ID);
          if (has_post_thumbnail( $post->ID )) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
          }
          array_push($rowArray, array('title' => $post_title, "post" => $post_url, "image" => $image[0]));
      }
      self::createView($rowArray);

      echo $after_widget;         

    } //End of function widget
 
    public static function createView($rowArray) {
      echo "<div id = 'trending'>
            <table>";
      if (count($rowArray) > 0) {
        foreach ($rowArray as $row) {
          echo "<tr><td>";
          $title = $row['title'];
          $post_url = $row['post'];
          $image = $row['image'];
          echo "<a href = '" . $post_url . "'>";
          echo "<div style = 'background-image: url(\"" . $image . "\");' class='trending-row'>";
          echo $title;
          echo "</div>";
          echo "</a></tr></td>";
        }
      }
      echo "</table>
            </div>";
    }
    /**
      * Sanitize widget form values as they are saved.
      *
      * @see WP_Widget::update()
      *
      * @param array $new_instance Values just sent to be saved.
      * @param array $old_instance Previously saved values from database.
      *
      * @return array Updated safe values to be saved.
      */

    public function update( $new_instance, $old_instance ) {               
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['count'] = strip_tags( $new_instance['count'] );
        $instance['category'] = strip_tags( $new_instance['category'] );
        return $instance;    
    }
  
    /**
      * Back-end widget form.
      *
      * @see WP_Widget::form()
      *
      * @param array $instance Previously saved values from database.
      */
    public function form( $instance ) {    
        $title      = esc_attr( $instance['title'] );
        $count      = esc_attr( $instance['count'] );
        $category      = esc_attr( $instance['category'] );
       
        if($count == "") {
          $count = 5;
        }

        if($title == "") {
          $title = "Trending";
        }

        if ($category == "" || empty($category)) {
           $category = "trending";
        }


?>
         
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title of Widget:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of posts to display:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" value="<?php echo $count; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo $category; ?>" />
        </p>
<?php
      
    }
     
}
 
/* Register the widget */
add_action( 'widgets_init', function(){
     register_widget( 'TopStories_Widget' );
});

add_action('wp_head', function(){
    wp_register_style('topstories', plugins_url('style.css',__FILE__ ));
    wp_enqueue_style('topstories');
});

?>
