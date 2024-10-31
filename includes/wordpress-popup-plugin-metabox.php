<?php

function wpp_featured_meta() {
    add_meta_box( 'wpp_meta', __( 'Wordpress Popup Plugin', 'wpp-textdomain' ), 'wpp_meta_callback', 'page', 'side', 'low' );
}
add_action( 'add_meta_boxes', 'wpp_featured_meta' );
 
/**
 * Outputs the content of the meta box
 */
 
function wpp_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'wpp_nonce' );
    $wpp_stored_meta = get_post_meta( $post->ID );
    ?>
	 <p>
	    <span class="wpp-row-title"><?php _e( 'Check this checkbox if you allow popup for this page:', 'wpp-textdomain' )?></span>
	    <div class="wpp-row-content">
	        <label for="allow-popup">
	            <input type="checkbox" name="allow-popup" id="allow-popup" value="yes" <?php if ( isset ( $wpp_stored_meta['allow-popup'] ) ) checked( $wpp_stored_meta['allow-popup'][0], 'yes' ); ?> />
	            <?php _e( 'Allow Popup', 'wpp-textdomain' )?>
	        </label>
	    </div>
	</p>
    <?php
}
 
/**
 * Saves the popup meta input
 */
function wpp_meta_save( $post_id ) {
 
    // Checks save status - overcome autosave, etc.
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'wpp_nonce' ] ) && wp_verify_nonce( $_POST[ 'wpp_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
	// Checks for input and saves - save checked as yes and unchecked at no
	if( isset( $_POST[ 'allow-popup' ] ) ) {
	    update_post_meta( $post_id, 'allow-popup', 'yes' );
	} else {
	    update_post_meta( $post_id, 'allow-popup', 'no' );
	}
 
}
add_action( 'save_post', 'wpp_meta_save' );