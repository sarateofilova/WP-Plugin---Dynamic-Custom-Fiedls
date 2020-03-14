<?php 
/**
 * Plugin Name: Dynamic Custom Fields
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Sara Teofilova
 * Author URI: http://www.mywebsite.com
 */


add_action('admin_init', 'hhs_add_meta_boxes', 1);
function hhs_add_meta_boxes() {
	add_meta_box( 'repeatable-fields', 'Repeatable Fields', 'hhs_repeatable_meta_box_display', 'cities', 'normal', 'default');
}

function hhs_repeatable_meta_box_display() {
	global $post;

	$repeatable_fields = get_post_meta($post->ID, 'repeatable_fields', true);

	wp_nonce_field( 'hhs_repeatable_meta_box_nonce', 'hhs_repeatable_meta_box_nonce' );
	?>
	<script type="text/javascript">
	jQuery(document).ready(function( $ ){
		$( '#add-row' ).on('click', function() {
			var row = $( '.empty-row.screen-reader-text' ).clone(true);
			row.removeClass( 'empty-row screen-reader-text' );
			row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
			return false;
		});
  	
		$( '.remove-row' ).on('click', function() {
			$(this).parents('tr').remove();
			return false;
		});
	});
	</script>
  
	<table id="repeatable-fieldset-one" width="100%">
	<thead>
		<tr>
			<th width="15%">Обект</th>
			<th width="15%">Град</th>
			<th width="30%">Монтирано оборудване</th>
			<th width="8%">Снимки</th>
			<th width="8%"></th>
		</tr>
	</thead>
	<tbody>
	<?php
	
	if ( $repeatable_fields ) :
	
	foreach ( $repeatable_fields as $field ) {
	?>
	<tr>
		<td><input type="text" class="widefat" name="object[]" value="<?php if($field['object'] != '') echo esc_attr( $field['object'] ); ?>" /></td>
		
		<td><input type="text" class="widefat" name="city[]" value="<?php if($field['city'] != '') echo esc_attr( $field['city'] ); ?>" /></td>
		
		<td><input type="text" class="widefat" name="equipment[]" value="<?php if($field['equipment'] != '') echo esc_attr( $field['equipment'] ); ?>" /></td>
	
		<td><input type="text" class="widefat" name="fotos[]" value="<?php if ($field['fotos'] != '') echo esc_attr( $field['fotos'] ); ?>" /></td>
	
		<td><a class="button remove-row" href="#">Remove</a></td>
	</tr>
	<?php
	}
	else :
	// show a blank one
	?>
	<tr>
		<td><input type="text" class="widefat" name="object[]" /></td>
	
		<td><input type="text" class="widefat" name="city[]" /></td>
		
		<td><input type="text" class="widefat" name="equipment[]" /></td>
	
		<td><input type="text" class="widefat" name="fotos[]" /></td>
	
		<td><a class="button remove-row" href="#">Remove</a></td>
	</tr>
	<?php endif; ?>
	
	<!-- empty hidden one for jQuery -->
	<tr class="empty-row screen-reader-text">
		<td><input type="text" class="widefat" name="object[]" /></td>
	
		<td><input type="text" class="widefat" name="city[]" /></td>
		
		<td><input type="text" class="widefat" name="equipment[]" /></td>
		
		<td><input type="text" class="widefat" name="fotos[]" /></td>
		  
		<td><a class="button remove-row" href="#">Remove</a></td>
	</tr>
	</tbody>
	</table>
	
	<p><a id="add-row" class="button" href="#">Add another</a></p>
	<?php
}

add_action('save_post', 'hhs_repeatable_meta_box_save');
function hhs_repeatable_meta_box_save($post_id) {
	if ( ! isset( $_POST['hhs_repeatable_meta_box_nonce'] ) ||
	! wp_verify_nonce( $_POST['hhs_repeatable_meta_box_nonce'], 'hhs_repeatable_meta_box_nonce' ) )
		return;
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;
	
	if (!current_user_can('edit_post', $post_id))
		return;
	
	$old = get_post_meta($post_id, 'repeatable_fields', true);
	$new = array();
	
	$objects = $_POST['object'];
	$cities = $_POST['city'];
	$equipments = $_POST['equipment'];
	$fotos = $_POST['fotos'];
	
	$count = count( $objects );
	
	for ( $i = 0; $i < $count; $i++ ) {
		if ( $objects[$i] != '' ) :
			$new[$i]['object'] = stripslashes( strip_tags( $objects[$i] ) );
			$new[$i]['city'] = stripslashes( strip_tags( $cities[$i] ) );
			$new[$i]['equipment'] = stripslashes( strip_tags( $equipments[$i] ) );
			$new[$i]['fotos'] = stripslashes( strip_tags( $fotos[$i] ) );
			
		
		endif;
	}

	if ( !empty( $new ) && $new != $old )
		update_post_meta( $post_id, 'repeatable_fields', $new );
	elseif ( empty($new) && $old )
		delete_post_meta( $post_id, 'repeatable_fields', $old );
}
?>