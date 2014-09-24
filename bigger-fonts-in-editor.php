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

// enqueueing jquery.cookie.js
add_action( 'admin_enqueue_scripts', 'bfe_load_scripts' );
function bfe_load_scripts() {
	wp_enqueue_script( 'bfe-set-cookie', plugin_dir_url( __FILE__ ) . 'js/jquery.cookie.js', array('jquery'), null );
}


// adding toolbar menu.
add_action( 'admin_bar_menu', 'bfe_admin_bar_menu', 999 );
function bfe_admin_bar_menu( $wp_admin_bar ) {
	
	// return if it's not admin or it's not post pages.
	global $pagenow;
	if ( !is_admin() || !in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
		return;
	}
	
	$args = array(
		'id'    => 'bigger-fonts-in-editor',
		'title' => __( 'Bigger Fonts', 'bigger-fonts-in-editor' ),
		'href'  => '#',
		'meta'  => array( 
			'class'          => 'bigger-fonts-in-editor',
			)
	);
	$wp_admin_bar->add_node( $args );

	
	$font_sizes = apply_filters( 'bfe_font_sizes', array( '1rem', '1.25rem', '1.35rem', '1.5rem', '1.75rem', '2rem' ) );
	foreach ( $font_sizes as $fs ) {
		$args = array(
			'id'     => esc_attr( $fs ),
			'title'  => $fs,
			'href'   => '#',
			'parent' => 'bigger-fonts-in-editor',
			'meta'   => array(
				'class' => 'bfe-font-change'
			)
		);
		$wp_admin_bar->add_node( $args );
	}

}

// 
add_action( 'admin_print_footer_scripts', 'bfe_admin_footer_script', 99999 );
function bfe_admin_footer_script() {
	
	// set line height to 1.6 by default.
	// line-height can be filtered, other elements can be added.
	$styles = apply_filters( 'bfe_styles', array( 'line-height' => '1.5em' ) );
	?>
<script type="text/javascript">
(function($){

	$(window).load(function() {
		console.log($('#content').html());
		if( $('#content').length > 0 ){
			size  = $.cookie('bfeSize');
			$('iframe#content_ifr').contents().find('.mceContentBody *').css('font-size', size);
		}
	});


	$('.bfe-font-change a').click(function(e) {
		
		e.preventDefault();

		size  = $(this).text();
		
		/* set font size */
		fonts = $('iframe#content_ifr').contents().find('.mceContentBody *');
		fonts.css('font-size', size);
		$.cookie('bfeSize', size);
		
		<?php 
		// add other styles.
		foreach ( $styles as $key => $val ): ?>
		fonts.css('<?php echo esc_js($key); ?>', '<?php echo esc_js($val) ?>');
		<?php endforeach; ?>
	
	});
})(jQuery);


/*
jQuery(window).load( function() {

    if( jQuery('#content').length > 0 ){
		size  = jQuery.cookie('bfeSize');
		contents = jQuery('iframe#content_ifr').contents().find('.mceContentBody *').css('font-size', size);
    }

});
*/

</script>
<?php
}








