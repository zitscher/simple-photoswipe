<?php

//Admin CSS
function photoswipe_admin_styles() {
	echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('style/photoswipe-admin.css', __FILE__ ) . '" />';
}
add_action('admin_head', 'photoswipe_admin_styles');

class simple_photoswipe_options
{

	public static function get_plugin_options()
	{
		$options = get_option('photoswipe_options');

		if (!is_array($options)) {
			$options['bar_size'] = '44';
			$options['indexIndicatorSep'] = '/';

			$options['loop_images'] = true;
			$options['show_close_element'] = true;
			$options['show_caption_element'] = true;
			$options['show_fullscreen_element'] = true;
			$options['show_zoom_element'] = true;
			$options['show_share_element'] = true;
			$options['show_counter_element'] = true;
			$options['show_arrow_element'] = true;
			$options['show_preloader_element'] = true;
			$options['tap_to_close'] = false;
			$options['tap_to_toggle_controls'] = true;

			update_option('photoswipe_options', $options);
		}

		return $options;
	}

	public static function render()
	{
		if (isset($_POST['photoswipe_save'])) {
			$options = simple_photoswipe_options::get_plugin_options();

			$options['bar_size'] = stripslashes($_POST['bar_size']);
			$options['loop_images'] = isset($_POST['loop_images']) ? (bool)true : (bool)false;
			$options['show_close_element'] = isset($_POST['show_close_element']) ? (bool)true : (bool)false;
			$options['show_caption_element'] = isset($_POST['show_caption_element']) ? (bool)true : (bool)false;
			$options['show_fullscreen_element'] = isset($_POST['show_fullscreen_element']) ? (bool)true : (bool)false;
			$options['show_zoom_element'] = isset($_POST['show_zoom_element']) ? (bool)true : (bool)false;
			$options['show_share_element'] = isset($_POST['show_share_element']) ? (bool)true : (bool)false;
			$options['show_counter_element'] = isset($_POST['show_counter_element']) ? (bool)true : (bool)false;
			$options['show_arrow_element'] = isset($_POST['show_arrow_element']) ? (bool)true : (bool)false;
			$options['show_preloader_element'] = isset($_POST['show_preloader_element']) ? (bool)true : (bool)false;
			$options['tap_to_close'] = isset($_POST['tap_to_close']) ? (bool)true : (bool)false;
			$options['tap_to_toggle_controls'] = isset($_POST['tap_to_toggle_controls']) ? (bool)true : (bool)false;

			$options['indexIndicatorSep'] = stripslashes($_POST['indexIndicatorSep']);

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
					<label for="thumbnail_width">Bar Size</label>
					<input type="text" id="bar_size" name="bar_size" value="<?php echo($options['bar_size']); ?>"/>
				</p>
				<p>
					<label for="indexIndicatorSep">Image Counter Separator</label>
					<input type="text" id="indexIndicatorSep" name="indexIndicatorSep" value="<?php echo($options['indexIndicatorSep']); ?>"/>
				</p>
				<p>
					<label for="loop_images">Loop Images</label>
					<input type="checkbox" id="loop_images" name="loop_images" value="<?php echo($options['loop_images']); ?>" <?php checked($options['loop_images'] == 1); ?>/>
				</p>

				<hr/>

				<h3>Photoswipe UI Elements</h3>
				<p>
					<label for="show_close_element">Show Close Button</label>
					<input type="checkbox" id="show_close_element" name="show_close_element" value="<?php echo($options['show_close_element']); ?>" <?php checked($options['show_close_element'] == 1); ?>/>
				</p>
				<p>
					<label for="show_fullscreen_element">Show Fullscreen Button</label>
					<input type="checkbox" id="show_fullscreen_element" name="show_fullscreen_element" value="<?php echo($options['show_fullscreen_element']); ?>" <?php checked($options['show_fullscreen_element'] == 1); ?>/>
				</p>
				<p>
					<label for="show_zoom_element">Show Zoom Button</label>
					<input type="checkbox" id="show_zoom_element" name="show_zoom_element" value="<?php echo($options['show_zoom_element']); ?>" <?php checked($options['show_zoom_element'] == 1); ?>/>
				</p>
				<p>
					<label for="show_share_element">Show Share Button</label>
					<input type="checkbox" id="show_share_element" name="show_share_element" value="<?php echo($options['show_share_element']); ?>" <?php checked($options['show_share_element'] == 1); ?>/>
				</p>
				<p>
					<label for="show_counter_element">Show Image Counter</label>
					<input type="checkbox" id="show_counter_element" name="show_counter_element" value="<?php echo($options['show_counter_element']); ?>" <?php checked($options['show_counter_element'] == 1); ?>/>
				</p>
				<p>
					<label for="show_arrow_element">Show Arrow Navigation</label>
					<input type="checkbox" id="show_arrow_element" name="show_arrow_element" value="<?php echo($options['show_arrow_element']); ?>" <?php checked($options['show_arrow_element'] == 1); ?>/>
				</p>
				<p>
					<label for="show_preloader_element">Show Image Preloader</label>
					<input type="checkbox" id="show_preloader_element" name="show_preloader_element" value="<?php echo($options['show_preloader_element']); ?>" <?php checked($options['show_preloader_element'] == 1); ?>/>
				</p>

				<h3>Tap Behaviour</h3>
				<p>
					<label for="tap_to_close">Tap Image to close</label>
					<input type="checkbox" id="tap_to_close" name="tap_to_close" value="<?php echo($options['tap_to_close']); ?>" <?php checked($options['tap_to_close'] == 1); ?>/>
				</p>
				<p>
					<label for="tap_to_toggle_controls">Tap to toggle controls</label>
					<input type="checkbox" id="tap_to_toggle_controls" name="tap_to_toggle_controls" value="<?php echo($options['tap_to_toggle_controls']); ?>" <?php checked($options['tap_to_toggle_controls'] == 1); ?>/>
				</p>

				<p>
					<input class="button-primary" type="submit" name="photoswipe_save" value="Save Settings"/>
				</p>
			</form>
		</div>
	<?php
	}
}

// register functions
add_action('admin_menu', array('simple_photoswipe_options', 'render'));