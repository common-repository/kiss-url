<?php
/*
Plugin Name: KISS URL
Plugin URI: http://wordpress.org/extend/plugins/kiss-url/
Version: 1.3
Author: G.R. Victor Johnson
Author URI: http://www.revood.com
Description: Automatically generates <a href="http://bit.ly">bit.ly</a> shortlink for every posts, pages, category, archives etc. To get started, you'll need a free bit.ly user account and apiKey. Signup at: http://bit.ly/a/sign_up.After activating the plugin go to Settings->KISS URL enter your bit.ly username and apiKey and save.
Licence: GPLv2
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function kissURL_settings() {
	register_setting( 'kissurl', 'kissurl_bitly_uname' );
	register_setting( 'kissurl', 'kissurl_bitly_api' );
	register_setting( 'kissurl', 'kissurl_after_content' );
}
add_action( 'admin_init', 'kissURL_settings' );

function kissURL_menu() {
	add_options_page( 'KISS URL settings', 'KISS URL', 'manage_options', 'kissurl', 'kissURL_page' );
}
add_action( 'admin_menu', 'kissURL_menu' );

function kissURL_page() {
	?>
	<div class="wrap">
		<h2><?php _e( 'KISS URL settings' ); ?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'kissurl' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e('bit.ly username'); ?>:</th>
					<td><input type="text" name="kissurl_bitly_uname" value="<?php echo get_option('kissurl_bitly_uname'); ?>" /></td>
				</tr>
				
				<tr>
					<th scope="row"><?php _e('bit.ly API key'); ?></th>
					<td><input type="text" name="kissurl_bitly_api" value="<?php echo get_option('kissurl_bitly_api'); ?>" /></td>
				</tr>
				
				<tr>
					<th scope="row"><?php _e('Show shortlink after content/excerpt'); ?></th>
					<td><input type="checkbox" name="kissurl_after_content" value="1" <?php if( get_option( 'kissurl_after_content' ) ) echo 'checked="checked"'; ?> /></td>
				</tr>
			</table>
		<p class="submit"><input type="submit" value="Submit" class="primary-button"  /></p>
		</form>
		<p>
			Developed by <a href="http://www.revood.com">Victor Gunday</a>
		</p>
	</div>
	<?php
}

function kissURL_short( $post_id = false ){
	global $post;
	
	if( !$post_id ) {
		if( $post )
			$post_id = $post->ID;
		else
			$post_id = get_queried_object_id();	
	}
	
	$url = get_permalink( $post_id );
	$shortlink = false;
	
	if( get_post_meta( $post_id, 'kissurl', true ) )
		return get_post_meta( $post_id, 'kissurl', true );
	
	$req = _build_api_request( $url );
	
	if( !$req )
		return false;
		
	$response = json_decode( @file_get_contents( $req ) );
	
	if( $response && $response->status_code == '200' ) {
		$shortlink = $response->data->url;
		update_post_meta( $post_id, 'kissurl', $shortlink );
	}
	
	return $shortlink;
}

function kissURL_adminbar_shortlink() {
	if( is_home() || is_front_page() )
		return false;
	
	return kissURL_short();
}
add_filter( 'pre_get_shortlink', 'kissURL_adminbar_shortlink' );

function kissurl_after_content( $content ) {
	if( !get_option( 'kissurl_after_content' ) )
		return $content;
	
	$shortlink = kissURL_short();
	if( $shortlink ) {
		if( is_feed() )
			$content .= '<p>Short link: '.$shortlink.'</p>';
		else
			$content .= '<p><input style="width: 200px;" type="text" value="'.$shortlink.'" /></p>';
	}
	
	return $content;
	
}
add_filter( 'the_content', 'kissurl_after_content' );
add_filter( 'the_excerpt', 'kissurl_after_content' );

function _build_api_request( $url = false ) {
	if( !$url )
		return false;
	
	$uname = get_option('kissurl_bitly_uname');
	$api = get_option('kissurl_bitly_api');
	
	if( !$uname || !$api )
		return false;
	
	$end_point = 'http://api.bit.ly/v3/shorten/?';
	$end_point .= 'login='.$uname;
	$end_point .= '&apiKey='.$api;
	$end_point .= '&longUrl='.rawurlencode( $url );
	$end_point .= '&format=json';
	
	return $end_point;
}

function kiss_shortcode_handler( $atts, $content = null, $code = "" ) {	
	extract( shortcode_atts( array( 'p' => null ), $atts ) );
	
	$url = kissURL_short( $p );
	
	if( !$content )
		return $url;
	
	return '<a href="' .$url. '">' .$content. '</a>';
}
add_shortcode('kiss-url', 'kiss_shortcode_handler');
?>
