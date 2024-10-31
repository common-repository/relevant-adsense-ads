<?php
/*
Plugin Name: Relevant AdSense Ads
Plugin URI: http://fredrikmalmgren.com/wordpress/plugins/relevant-adsense-ads/
Description: Help Google AdSense show more relevant ads on your site by emphasize relevant content or downplay content that is not relevant.
Version: 0.1.0
Author: Fredrik Malmgren	
Author URI: http://fredrikmalmgren.com/
*/

$relevant_adsense_ads = new relevant_adsense_ads;

class relevant_adsense_ads {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );		
	}

	function init() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_shortcode('relevant-to-adsense', array( $this, 'function_relevant_for_adsense' ));

		if(get_option( 'rcga_auto' ) == true ){
			add_filter('the_content', array( $this, 'make_auto_relevant' ), 1000 );
		}			
	}

	function admin_init() {
		register_setting( 'relevant_adsense_ads_settings_page', 'relevant_adsense_ads' );
		add_settings_section( 'default', 'Help Google AdSense find relevant content', array( $this, 'option_content' ), 'relevant_adsense_ads_settings_page' );

		register_setting( 'relevant_adsense_ads_settings_page', 'rcga_auto' );
		add_settings_field( 'rcga_auto', 'Enable auto relevant ', array( $this, 'auto_relevant' ), 'relevant_adsense_ads_settings_page', 'default' );	
	}	
	
	function admin_menu() {
		add_options_page( 'Relevant Adsense Ads - Options', 'Relevant AdSense Ads', 'manage_options', 'relevant_adsense_ads', array( $this, 'options' ) );        
	}

	function options() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>Relevant Adsense Ads</h2>';
		echo '<form action="options.php" method="post">';
		settings_fields('relevant_adsense_ads_settings_page');
		do_settings_sections('relevant_adsense_ads_settings_page');
		echo '<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></p>';
		echo '</form></div>';
		echo '<h4>Manually tag relevant content with shortcodes</h4>';
		echo '<p>With the shordcodes listed here you can manually tag relevant content but also exclude content that Google AdSense should ignore.</p>';
		echo '<p>Start relevant content section: <code>[relevant-to-adsense type="start"]</code> or just <code>[relevant-to-adsense]</code></p>';
		echo '<p>Start ignore content section: <code>[relevant-to-adsense type="ignore"]</code></p>';
		echo '<p>Stop relevant/ignore content section: <code>[relevant-to-adsense type="stop"]</code></p>';
	}
	
	function auto_relevant() {
?>
	<input type="checkbox" name="rcga_auto" id="rcga_auto" value="1" <?php checked( (bool) get_option( 'rcga_auto' ) ); ?>" />	
<?php
	}
	
	function option_content() {
		echo "<h4>Automatically tag relevant content</h4>
			<p>Choose to let WordPress automatically tag your content as relevant for Google AdSense.</p>";
	}

	function make_auto_relevant($content) {
			
		$start = '<!-- google_ad_section_start -->';
		$stop = '<!-- google_ad_section_end -->';
		
		$content = $start.$content.$stop;
			
		return $content;
	}
	
	function function_relevant_for_adsense( $atts ) {
		extract(shortcode_atts(array(
		'type' => 'start',
		), $atts));
		
		remove_filter('the_content', array( $this, 'make_auto_relevant' ), 1000 );
		
		switch ($type) {
			case start :
			$code = '<!-- google_ad_section_start -->';
			break;
			case ignore :
			$code = '<!-- google_ad_section_start(weight=ignore) -->';
			break;
			case stop :
			$code = '<!-- google_ad_section_end -->';
			break;
		}

		return $code;
	}
	
}

?>