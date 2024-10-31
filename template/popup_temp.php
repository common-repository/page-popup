<?php
function popup_load() {
	global $post;
	$popup = get_post_meta( $post->ID,'allow-popup',true );

	if ($popup == 'yes') {
		$bg_image = wp_get_attachment_image_src(get_option( 'wpp_background_image' ),'full')[0];
		$popup_width = get_option( 'wpp_popup_width' );
		$popup_height = get_option( 'wpp_popup_height' );
		$wpp_overlay_opacity = get_option( 'wpp_overlay_opacity' );
		
		$popup_html = '<div class="md-modal '.get_option( 'wpp_popup_effect' ).'" id="modal-1" style="width:'.$popup_width.'; height:'.$popup_height.';">
			<div class="md-content" style="'.(get_option( 'wpp_background_image' )!='' ? 'background-image:url('.$bg_image.')' : 'background-color:'.get_option( 'wpp_background_colour' )).'; ">
				<div class="modal-desc"><h3 style="color:'.get_option( 'wpp_title_colour' ).';">'.get_option( 'wpp_title' ).'</h3>
				<p style="color:'.get_option( 'wpp_description_colour' ).';">'.get_option( 'wpp_description' ).'</p>
				<div class="modal_close"></div></div>
				<div class="popup__submit"><a href="'.get_option( 'wpp_button_link' ).'" style="color:'.get_option( 'wpp_button_text_colour' ).'; background-color:'.get_option( 'wpp_button_colour' ).';" class="popup__btn">'.get_option( 'wpp_button_text' ).'</a></div>
			</div>
			
			<input type="hidden" class="popup__timeout" value="'.get_option( 'wpp_popup_timeout' ).'"></input>
			<input type="hidden" class="popup__display" value="'.get_option( 'wpp_display_single' ).'"></input>
		</div><div class="md-overlay" style="background-color:'.get_option( 'wpp_overlay_colour' ).'; opacity:'.$wpp_overlay_opacity.';"></div>';
		echo $popup_html;
	}
}
add_action( 'wp_footer', 'popup_load' );
?>