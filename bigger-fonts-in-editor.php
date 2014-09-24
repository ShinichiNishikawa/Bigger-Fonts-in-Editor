<?php
/*
Plugin Name: Bigger Fonts in Editor
Version:     0.1
Description: This plugin makes the fonts in your WordPress visual editor bigger.
Author:      ShinichiN
Contributor: 
Author URI:  http://th-daily.shinichi.me/
Plugin URI: 
Text Domain: bigger-fonts-in-editor
Domain Path: /languages
*/

// adding toolbar menu.
add_action( 'admin_bar_menu', 'bfe_admin_bar_menu', 999 );
function bfe_admin_bar_menu( $wp_admin_bar ) {
	
	// return if it's not admin or it's not post pages.
	if ( !bfe_is_postpage() ) {
		return;
	}

	// add parent node
	$args = array(
		'id'    => 'bigger-fonts-in-editor',
		'title' => __( 'Bigger Fonts', 'bigger-fonts-in-editor' ),
		'href'  => '#',
		'meta'  => array( 
			'class' => 'bigger-fonts-in-editor',
			)
	);
	$wp_admin_bar->add_node( $args );

	// add children
	$font_sizes = array(
		__('Reset', 'bigger-fonts-in-editor') => '100%',
		'125%' => '125%',
		'150%' => '150%',
		'175%' => '175%',
		'200%' => '200%',
		'300%' => '300%',
	);
	$font_sizes = apply_filters( 'bfe_font_sizes', $font_sizes );
	foreach ( $font_sizes as $text => $fs ) {
		$args = array(
			'id'     => esc_attr( $fs ),
			'title'  => $text,
			'href'   => '#',
			'parent' => 'bigger-fonts-in-editor',
			'meta'   => array(
				'class'        => 'bfe-font-change',
			)
		);
		$wp_admin_bar->add_node( $args );
	}
}

// add scripts to the admin page.
add_action( 'admin_print_footer_scripts', 'bfe_admin_footer_script', 99999 );
function bfe_admin_footer_script() {

	// return if it's not admin or it's not post pages.
	if ( !bfe_is_postpage() ) {
		return;
	}
	
	// set line height to 1.6 by default.
	// line-height can be filtered, other elements can be added.
	$styles = apply_filters( 'bfe_styles', array( 'line-height' => '1.5em' ) );
	?>
<script type="text/javascript">
(function($){

	<?php // when the tinymce contents is loaded, get the cookie & change fontsize ?>
	$(window).load(function() {
		if( $('#content').length > 0 ){
			var bfeSize  = $.cookie('bfeSize');
			$('iframe#content_ifr').contents().find('.mceContentBody *').css('font-size', bfeSize);
		}
	});

	<?php // when clicked, get value out of parents id & change fontsize ?>
	$('.bfe-font-change a').click(function(e) {
		
		e.preventDefault();
		
		var bfeSize  = $(this).parent().attr('id').replace( 'wp-admin-bar-', '');		
		var fonts = $('iframe#content_ifr').contents().find('.mceContentBody *');
		
		fonts.css('font-size', bfeSize);
		$.cookie('bfeSize', bfeSize);
		
		<?php 
		// add other styles.
		foreach ( $styles as $key => $val ): ?>
		fonts.css('<?php echo esc_js($key); ?>', '<?php echo esc_js($val) ?>');
		<?php endforeach; ?>
	
	});
})(jQuery);

</script>
<?php
}

// this returns true when you are in post.php or post-new.php
function bfe_is_postpage() {
	global $pagenow;
	if ( is_admin() && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
		return true;
	}
	
	return false;
}

// enqueueing jquery.cookie.js
add_action( 'admin_enqueue_scripts', 'bfe_load_scripts' );
function bfe_load_scripts() {

	// return if it's not admin or it's not post pages.
	if ( !bfe_is_postpage() ) {
		return;
	}

	wp_enqueue_script( 'bfe-set-cookie', plugin_dir_url( __FILE__ ) . 'js/jquery.cookie.js', array('jquery'), null );
}
