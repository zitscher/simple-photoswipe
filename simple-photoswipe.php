<?php
/*
Plugin Name: Simple Photoswipe
Plugin URI: https://github.com/zitscher/simple-photoswipe
Description: A simple plugin for galleries using PhotoSwipe from Dmitry Semenov.
Author: Tobias Cichon
Author URI:
Version: 0.1
License: MIT
*/

defined('ABSPATH') or die("No script kiddies please!");

function enqueue_scripts() {
	$plugin_path =  plugins_url() . '/simple-photoswipe' ;

	wp_enqueue_style(  'photoswipe-core-css',		$plugin_path . '/lib/photoswipe.css');
	wp_enqueue_style(  'photoswipe-default-skin',	$plugin_path . '/lib/default-skin/default-skin.css');
	wp_enqueue_script( 'photoswipe', 				$plugin_path . '/lib/photoswipe.min.js');
	wp_enqueue_script( 'photoswipe-ui-default', 	$plugin_path . '/lib/photoswipe-ui-default.min.js');
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');

// append photoswipe template to DOM
include_once('photoswipe_template.php');

// change wp gallery output to match with photoswipe
include_once('custom_wp_gallery_output.php');

// show admin photoswipe options
include_once('admin-options.php');


function injectPhotoswipeInitScript() {
	$script = "
		<script>document.addEventListener('DOMContentLoaded', function() {
			var initPhotoSwipeFromDOM = function(gallerySelector) {

				// parse slide data (url, title, size ...) from DOM elements
				// (children of gallerySelector)
				var parseThumbnailElements = function(el) {
					var thumbElements = el.childNodes,
						numNodes = thumbElements.length,
						items = [],
						figureEl,
						childElements,
						linkEl,
						size,
						item;

					for(var i = 0; i < numNodes; i++) {
						figureEl = thumbElements[i]; // <figure> element

						// include only element nodes
						if(figureEl.nodeType !== 1) {
							continue;
						}

						linkEl = figureEl.children[0]; // <a> element
						size = linkEl.getAttribute('data-size').split('x');

						// create slide object
						item = {
							src: linkEl.getAttribute('href'),
							w: parseInt(size[0], 10),
							h: parseInt(size[1], 10)
						};

						if(figureEl.children.length > 1) {
							// <figcaption> content
							item.title = figureEl.children[1].innerHTML;
						}

						if(linkEl.children.length > 0) {
							// <img> thumbnail element, retrieving thumbnail url
							item.msrc = linkEl.children[0].getAttribute('src');
						}

						item.el = figureEl; // save link to element for getThumbBoundsFn
						items.push(item);
					}

					return items;
				};

				// find nearest parent element
				var closest = function closest(el, fn) {
					return el && ( fn(el) ? el : closest(el.parentNode, fn) );
				};

				// triggers when user clicks on thumbnail
				var onThumbnailsClick = function(e) {
					e = e || window.event;
					e.preventDefault ? e.preventDefault() : e.returnValue = false;

					var eTarget = e.target || e.srcElement;

					var clickedListItem = closest(eTarget, function(el) {
						return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
					});

					if(!clickedListItem) {
						return;
					}


					// find index of clicked item
					var clickedGallery = clickedListItem.parentNode,
						childNodes = clickedListItem.parentNode.childNodes,
						numChildNodes = childNodes.length,
						nodeIndex = 0,
						index;

					for (var i = 0; i < numChildNodes; i++) {
						if(childNodes[i].nodeType !== 1) {
							continue;
						}

						if(childNodes[i] === clickedListItem) {
							index = nodeIndex;
							break;
						}
						nodeIndex++;
					}



					if(index >= 0) {
						openPhotoSwipe( index, clickedGallery );
					}
					return false;
				};

				// parse picture index and gallery index from URL (#&pid=1&gid=2)
				var photoswipeParseHash = function() {
					var hash = window.location.hash.substring(1),
						params = {};

					if(hash.length < 5) {
						return params;
					}

					var vars = hash.split('&');
					for (var i = 0; i < vars.length; i++) {
						if(!vars[i]) {
							continue;
						}
						var pair = vars[i].split('=');
						if(pair.length < 2) {
							continue;
						}
						params[pair[0]] = pair[1];
					}

					if(params.gid) {
						params.gid = parseInt(params.gid, 10);
					}

					if(!params.hasOwnProperty('pid')) {
						return params;
					}
					params.pid = parseInt(params.pid, 10);
					return params;
				};

				var openPhotoSwipe = function(index, galleryElement) {
					var pswpElement = document.querySelectorAll('.pswp')[0],
						gallery,
						options,
						items;

					items = parseThumbnailElements(galleryElement);

					// define options (if needed)
					options = {
						index: index,
						history: true,
						focus: true,
						showHideOpacity: false,
						showAnimationDuration: 150,
						hideAnimationDuration: 150,
						bgOpacity: 0.8,
						spacing: 0.12,
						allowPanToNext: true,
						maxSpreadZoom: 2,
						loop: true,
						pinchToClose: true,
						closeOnScroll: true,
						closeOnVerticalDrag: true,
						escKey: true,
						arrowKeys: true,
						history: true,
						mainClass: 'awesome-photoswipe',

						// define gallery index (for URL)
						galleryUID: galleryElement.getAttribute('data-pswp-uid'),

						getThumbBoundsFn: function(index) {
							// See Options -> getThumbBoundsFn section of docs for more info
							var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
								pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
								rect = thumbnail.getBoundingClientRect();

							return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
						}
					};

					// Pass data to PhotoSwipe and initialize it
					gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
					gallery.init();
				};

				// loop through all gallery elements and bind events
				var galleryElements = document.querySelectorAll( gallerySelector );

				for(var i = 0, l = galleryElements.length; i < l; i++) {
					galleryElements[i].setAttribute('data-pswp-uid', i+1);
					galleryElements[i].onclick = onThumbnailsClick;
				}

				// Parse URL and open gallery if it contains #&pid=3&gid=1
				var hashData = photoswipeParseHash();
				if(hashData.pid > 0 && hashData.gid > 0) {
					openPhotoSwipe( hashData.pid - 1 ,  galleryElements[ hashData.gid - 1 ], true );
				}
			};

			// init photoswipe
			initPhotoSwipeFromDOM('.gallery');
		});
	</script>";

	echo $script;
}

add_action('wp_footer', 'injectPhotoswipeInitScript');