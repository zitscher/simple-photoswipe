<?php
	function add_data_size ($content, $id, $size, $permalink, $icon, $text) {
		if ($permalink) {
			return $content;
		}

		$image_attributes = wp_get_attachment_image_src( $id, 'full' );
		$content = preg_replace("/<a/","<a data-size=\"" . $image_attributes[1] . "x" . $image_attributes[2] . "\"", $content, 1);
		return $content;
	}
	add_filter( 'wp_get_attachment_link', 'add_data_size', 10, 6);


	// Custom filter function to modify default gallery shortcode output
	function my_post_gallery( $output, $attr ) {

		// Initialize
		global $post, $wp_locale;

		// Gallery instance counter
		static $instance = 0;
		$instance++;

		// Validate the author's orderby attribute
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] ) unset( $attr['orderby'] );
		}

		// Get attributes from shortcode
		extract( shortcode_atts( array(
									 'order'      => 'ASC',
									 'orderby'    => 'menu_order ID',
									 'id'         => $post->ID,
									 'itemtag'    => 'figure',
									 'icontag'    => 'dt',
									 'captiontag' => 'figcaption',
									 'columns'    => 3,
									 'size'       => 'thumbnail',
									 'include'    => '',
									 'exclude'    => ''
								 ), $attr ) );

		// Initialize
		$id = intval( $id );
		$attachments = array();
		if ( $order == 'RAND' ) $orderby = 'none';

		if ( ! empty( $include ) ) {

			// Include attribute is present
			$include = preg_replace( '/[^0-9,]+/', '', $include );
			$_attachments = get_posts( array( 'include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );

			// Setup attachments array
			foreach ( $_attachments as $key => $val ) {
				$attachments[ $val->ID ] = $_attachments[ $key ];
			}

		} else if ( ! empty( $exclude ) ) {

			// Exclude attribute is present
			$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );

			// Setup attachments array
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
		} else {
			// Setup attachments array
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
		}

		if ( empty( $attachments ) ) return '';

		// Filter gallery differently for feeds
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) $output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
			return $output;
		}

		// Filter tags and attributes
		$itemtag = tag_escape( $itemtag );
		$captiontag = tag_escape( $captiontag );
		$columns = intval( $columns );
		$itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
		$float = is_rtl() ? 'right' : 'left';
		$selector = "gallery-{$instance}";

		// Filter gallery CSS
		$output = apply_filters( 'gallery_style', "
			<style type='text/css'>
				#{$selector} {
					/* Masonry container */
					-moz-column-gap: 1em;
					-webkit-column-gap: 1em;
					column-gap: 1em;
				}

				#{$selector} .gallery-item {
					display: inline-block;
					margin: 0 0 1em;
    				width: 100%;
				}

				#{$selector} .gallery-item img{
					width: 100%;
					height: auto;
				}

				/* #2- Portrait tablet to landscape and desktop */
				@media (min-width: 768px){
					#{$selector} {
						-moz-column-count: {$columns} !important;
						-webkit-column-count: {$columns} !important;
						column-count: {$columns} !important;
					}
				}

				/* #3- Landscape phone to portrait tablet */
				@media (min-width: 481px) and (max-width: 767px) {
					#{$selector} {
						-moz-column-count: 3 !important;
						-webkit-column-count: 3 !important;
						column-count: 3 !important;
					}
				}

				/* #4- Landscape phones and down */
				@media (max-width: 480px) {
					#{$selector} {
						-moz-column-count: 2 !important;
						-webkit-column-count: 2 !important;
						column-count: 2 !important;
					}
				}
			</style>
			<!-- see gallery_shortcode() in wp-includes/media.php -->
			<div id='$selector' class='gallery galleryid-{$id}'>"
		);

		// Iterate through the attachments in this gallery instance
		$i = 0;
		foreach ( $attachments as $id => $attachment ) {

			// Attachment link
			$link = isset( $attr['link'] ) && 'file' == $attr['link'] ? wp_get_attachment_link( $id, $size, false, false ) : wp_get_attachment_link( $id, $size, true, false );

			// Start itemtag
			$output .= "<{$itemtag} class='gallery-item'>";

			// icontag
			$output .= $link;

			if ( $captiontag && trim( $attachment->post_excerpt ) ) {

				// captiontag
				$output .= "
				<{$captiontag} class='gallery-caption'>
					" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";

			}

			// End itemtag
			$output .= "</{$itemtag}>";
		}

		// End gallery output
		$output .= "</div>\n";

		return $output;
	}

	// Apply filter to default gallery shortcode
	add_filter( 'post_gallery', 'my_post_gallery', 10, 2 );
?>