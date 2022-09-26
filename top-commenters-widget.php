<?php
/*
Plugin Name: WP Widget Plugin
Description: This is a sample plugin to learn custom widget development
Version: 1.0
Textdomain: tc-wp-widget
*/

class WP_Widget_Plugin extends WP_Widget {

	public function __construct() {

		// initialize widget name, id or other attributes
		parent::__construct(
				"tc-wp-widget",
				"TOP Comennters Widget",
				array(
						"description" => "Top Commeters Widget"
				) );

		add_action( "widgets_init", function () {
			register_widget( "WP_Widget_Plugin" );
		} );
	}

	public function form( $instance ) {

		// admin panel layout
		$instance['comment_qty_show']    = ! empty( $instance['comment_qty_show'] ) ? $instance['comment_qty_show'] : "";
		$instance['users_zero_comments'] = ! empty( $instance['users_zero_comments'] ) ? $instance['users_zero_comments'] : "";
		$users_in_top                    = ! empty( $instance['users_in_top'] ) ? $instance['users_in_top'] : "";
		$select_users_roles              = isset( $instance['select_users_roles'] ) ? $instance['select_users_roles'] : '';
		
		?>
		
      <p>
          <label for="<?php echo $this->get_field_id( 'comment_qty_show' ) ?>"><?php echo __( 'Show quantity comments in list', 'tc-wp-widget' ); ?></label>
          <input class="checkbox" type="checkbox" <?php checked( $instance['comment_qty_show'], 'on' ); ?>
                 value="on"
                 id="<?php echo $this->get_field_id( 'comment_qty_show' ); ?>"
                 name="<?php echo $this->get_field_name( 'comment_qty_show' ); ?>"/>
      </p>

      <p>
          <label for="<?php echo $this->get_field_id( 'users_in_top' ) ?>"><?php echo __( 'Display users in top', 'tc-wp-widget' ); ?></label>
          <input type="text" name="<?php echo $this->get_field_name( 'users_in_top' ) ?>"
                 id="<?php echo $this->get_field_id( 'users_in_top' ) ?>" value="<?php echo $users_in_top; ?>"
                 class="widefat"/>
      </p>

      <p>
          <label for="<?php echo $this->get_field_id( 'users_zero_comments' ) ?>"><?php echo __( 'Display users with zero comments count', 'tc-wp-widget' ); ?></label>
          <input class="checkbox" type="checkbox" <?php checked( $instance['users_zero_comments'], 'on' ); ?>
                 value="on"
                 id="<?php echo $this->get_field_id( 'users_zero_comments' ); ?>"
                 name="<?php echo $this->get_field_name( 'users_zero_comments' ); ?>"/>
      </p>
	  <p>
			<label for="<?php echo $this->get_field_id( 'select_users_roles' ) ?>"><?php echo __( 'Users Roles', 'tc-wp-widget' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'select_users_roles' ); ?>[]" id="<?php echo $this->get_field_id( 'select_users_roles' ); ?>" class="widefat" multiple>
				<?php $options = get_editable_roles();
				foreach ( $options as $key => $option ) {
					$selected = implode(',', (array)$select_users_roles);
					$select = in_array( $option["name"], (array)$select_users_roles  ) ? ' selected' : null;
					echo '<option value="' . $option['name'] . '" id="' . esc_attr( $key ) . '" '. $select . '>'. $option['name'] . '</option>';
				}
				?>
			</select>
			
		</p>
		<?php

	}

	public function widget( $args, $instance ) {

		extract( $args );

		$comment_qty_show    = $instance['comment_qty_show'] ? 'true' : 'false';
		$users_zero_comments = $instance['users_zero_comments'] ? 'true' : 'false';
		$users_in_top        = $instance['users_in_top'];
		$select_users_roles  = isset( $instance['select_users_roles'] ) ? $instance['select_users_roles'] : '';

		echo $before_widget;

		$args  = array(
				'role__in'   => $select_users_roles,
				'number' => $users_in_top
		);
		$users = new WP_User_Query( $args );

		if ( ! empty( $users->results ) ) {
			foreach ( $users->results as $user ) {
				$args     = array(
						'post_author' => $user->ID,
				);
				$comments = new WP_Comment_Query( $args );
				echo '<ul>';
				if ( $users_zero_comments == 'true' ) {
					if ( $comments->comments ) {
						if ( $comment_qty_show == 'true' ) {
							echo '<li>' . $user->display_name . ' (' . count( $comments->comments ) . ')</li>';
						} else {
							echo '<li>' . $user->display_name . '</li>';
						}

					} else {
						echo '<li>' . $user->display_name . '</li>';
					}
				} else {
					if ( ! empty( $comments->comments ) ) {
						if ( $comment_qty_show == 'true' ) {
							echo '<li>' . $user->display_name . ' (' . count( $comments->comments ) . ')</li>';
						} else {
							echo '<li>' . $user->display_name . '</li>';
						}
					}
				}
				echo '</ul>';
			}
		}

		echo $after_widget;

	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance                        = array();
		$instance['comment_qty_show']    = $new_instance['comment_qty_show'] ? 'on' : false;
		$instance['users_zero_comments'] = $new_instance['users_zero_comments'] ? 'on' : false;
		$instance['users_in_top']        = isset( $new_instance['users_in_top'] ) ? strip_tags( $new_instance['users_in_top'] ) : '';
		$instance['select_users_roles']  = isset( $new_instance['select_users_roles'] ) ? esc_sql( $new_instance['select_users_roles'] ) : '';

		return $instance;

	}
}

$wp_plugin_widget_object = new WP_Widget_Plugin();
