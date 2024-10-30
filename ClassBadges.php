<?php

/*
	Plugin Name: Classbadges Student Badges
	Plugin URI: http://wordpress.org/extend/plugins/classbadges-student-badges/
	Description: The ClassBadges plugin allows any student registered on <a href="http://classbadges.com">ClassBadges.com</a> to share badges in their public folder on a WordPress blog.
	Author: The ClassBadges Team
	Version: 1.0
	Author URI: http://classbadges.com/
	License:
	
	Copyright 2013 The ClassBadges Team (duncan@classbadges.com)
	
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

class Classbadges_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'ClassBadges',
			'ClassBadges',
			array( 'description' => __( 'Widget for sharing earned badges', '' ), )
		);
		wp_enqueue_script('jquery');

	}



	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		$res = wp_remote_get('http://classbadges.com/share/json?key=' . $instance['api_key']);
		$badges = json_decode($res['body'], true);
		if (!$badges) $badges = array();
		?>
		<style type="text/css">
			#cb_widget_badges_list {
				list-style-type: none;
				overflow: hidden;
			}
			#cb_widget_badges_list li {
				float: left;
				margin-right: 10px;
			}
			#cb_widget_badges_list li .details {
				display:none;
			}

		</style>
		<script type="text/javascript">
			jQuery('document').ready(function($) {
				$('#cb_widget_badges_list li').click(function() {
					$('body').unbind('click');
					var bp = $('#badge_popup_window');
					bp.html($(this).find('.details').html());
					bp.css('display', 'block');
					setTimeout(function() {
						$('body').click(function(e) {
							if (!bp.find($(e.target)).length) {
								$('body').unbind('click');
								bp.hide();
							}
						});
					}, 1);
				});
			});
		</script>
		<div id="badge_popup_window" style="display: none; left: 50%; position: fixed; width: 400px; top: 30%; padding: 20px; background-color: rgb(249, 249, 249); margin-left: -230px; -webkit-box-shadow: rgba(50, 50, 50, 0.298039) 0px 0px 7px; box-shadow: rgba(50, 50, 50, 0.298039) 0px 0px 7px; z-index: 999999;"></div>
		<ul id="cb_widget_badges_list">
			<?php 
				foreach ($badges as $k => $v) {
					$image_folder = (is_string($v['date_recommended'])) ? 'uploads/eei_badges/' : 'uploads/';
			?>
				<li>
					<img style="width: 70px;height:70px;" src="<?=$v['photo']?>" />
				</li>
			<?php } ?>
		</ul>
		<a style="text-decoration: none;height:40px;width: 115px;float: left;clear: both;margin-left: 3px;" target="_blank" href="http://classbadges.com">
			<img src="https://f7bfa71e3ec4a76ad35c-11f9f111464a294fef446439068e7f2f.ssl.cf1.rackcdn.com/images/faviconclassbadges.png" style="float: left;width: 30px;">
			<span style="float: left;margin-top: 8px;margin-left: 5px;font-size: 13px;">ClassBadges</span>
		</a>
		<?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['api_key'] = strip_tags( $new_instance['api_key'] );
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	public function form( $instance ) {
		$api_key = $instance[ 'api_key' ];
		$title 	= $instance[ 'title' ];
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'api_key' ); ?>"><?php _e( 'API Key:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" type="text" value="<?php echo esc_attr( $api_key ); ?>" />

		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

		</p>


		<?php 
	}

} 


add_action( 'widgets_init', create_function( '', 'register_widget( "Classbadges_Widget" );' ) );

?>