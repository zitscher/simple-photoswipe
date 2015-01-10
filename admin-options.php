<?php

//Admin CSS
function photoswipe_admin_styles() {
	echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('photoswipe-admin.css', __FILE__ ) . '" />';
}
add_action('admin_head', 'photoswipe_admin_styles');

class simple_photoswipe_options
{

	public static function get_plugin_options()
	{
		$options = get_option('photoswipe_options');

		if (!is_array($options)) {

			$options['show_controls'] = true;
			$options['loop_images'] = true;

			$options['thumbnail_width'] = 150;
			$options['thumbnail_height'] = 150;

			$options['max_image_height'] = '2400';
			$options['max_image_width'] = '1800';

			update_option('photoswipe_options', $options);
		}

		return $options;
	}

	public static function render()
	{

		if (isset($_POST['photoswipe_save'])) {

			$options = simple_photoswipe_options::get_plugin_options();

			$options['thumbnail_width'] = stripslashes($_POST['thumbnail_width']);
			$options['thumbnail_height'] = stripslashes($_POST['thumbnail_height']);

			$options['max_image_width'] = stripslashes($_POST['max_image_width']);
			$options['max_image_height'] = stripslashes($_POST['max_image_height']);


			if (isset($_POST['show_controls'])) {
				$options['show_controls'] = (bool)true;
			} else {
				$options['show_controls'] = (bool)false;
			}

			if (isset($_POST['loop_images'])) {
				$options['loop_images'] = (bool)true;
			} else {
				$options['loop_images'] = (bool)false;
			}

			update_option('photoswipe_options', $options);

		} else {
			simple_photoswipe_options::get_plugin_options();
		}

		add_submenu_page(
			'options-general.php', 'Simple PhotoSwipe', 'Simple PhotoSwipe', 'edit_theme_options', basename(__FILE__), array(
				'simple_photoswipe_options', 'display'
			)
		);
	}

	public static function display()
	{
		$options = simple_photoswipe_options::get_plugin_options(); ?>

		<div id="photoswipe_admin">
			<h2>Simple PhotoSwipe Options</h2>

			<form method="post" action="#" enctype="multipart/form-data">
				<p>
					<label for="show_controls">Show Controls</label>
					<input type="checkbox" id="show_controls" name="show_controls" value="<?php echo($options['show_controls']); ?>" <?php checked($options['show_controls'] == 1); ?>/>
				</p>

				<p>
					<label for="loop_images">Loop Images</label>
					<input type="checkbox" id="loop_images" name="loop_images" value="<?php echo($options['loop_images']); ?>" <?php checked($options['loop_images'] == 1); ?>/>
				</p>
				
<!--				<p>-->
<!--					<label for="thumbnail_width">Thumbnail Width</label>-->
<!--					<input type="text" id="thumbnail_width" name="thumbnail_width" value="--><?php //echo($options['thumbnail_width']); ?><!--"/>-->
<!--				</p>-->
<!---->
<!--				<p>-->
<!--					<label for="thumbnail_height">Thumbnail Height</label>-->
<!--					<input type="text" id="thumbnail_height" name="thumbnail_height" value="--><?php //echo($options['thumbnail_height']); ?><!--"/>-->
<!--				</p>-->
<!---->
<!--				<p>-->
<!--					<label id="max_image_width">Max image width</label>-->
<!--					<input type="text" id="max_image_width" name="max_image_width" value="--><?php //echo($options['max_image_width']); ?><!--"/>-->
<!--				</p>-->
<!---->
<!--				<p>-->
<!--					<label id="max_image_height">Max image height</label>-->
<!--					<input type="text" id="max_image_height" name="max_image_height" value="--><?php //echo($options['max_image_height']); ?><!--"/>-->
<!--				</p>-->

				<p>
					<input class="button-primary" type="submit" name="photoswipe_save" value="Save"/>
				</p>
			</form>
		</div>
	<?php
	}
}

// register functions
add_action('admin_menu', array('simple_photoswipe_options', 'render'));