<?php
/**
 * Plugin Name: WooCommerce Configurator
 * Description: Product configurator 
 * Author: Matija
 * Text Domain:woocommerce-configurator
 * Version: 0.1
 * WC requires at least: 4.2.0
 * WC tested up to: 4.2
 * Copyright (c) 2020 Matija
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Includes js

function wpdocs_selectively_enqueue_admin_script( $hook ) {
    wp_enqueue_script('wc_configurator_script_duplicate', plugin_dir_url( __FILE__ ) . 'duplicate.js', array('jquery'),null);
	wp_enqueue_script('wc_configurator_script_button', plugin_dir_url( __FILE__ ) . 'button.js', array('jquery'),null);
	wp_enqueue_script('wc_configurator_script_remove', plugin_dir_url( __FILE__ ) . 'remove.js', array('jquery'),null);	
}

function wpdocs_selectively_enqueue_script($hook){
	wp_enqueue_script('wc_configurator_script_toggle', plugin_dir_url( __FILE__ ) . 'toggleButton.js', array('jquery'),null);
}

add_action( 'admin_enqueue_scripts', 'wpdocs_selectively_enqueue_admin_script' );
add_action('wp_enqueue_scripts','wpdocs_selectively_enqueue_script');





if ( ! defined( 'ABSPATH' ) ) {
    exit;
} 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$tab=new CustomTab();

}

// Class containing functions for product data tabs

class CustomTab{
	
	public function __construct(){
		add_filter('woocommerce_product_data_tabs',[$this,'add_tab'],10,1);		
		add_filter('woocommerce_product_data_panels',[$this,'tab_content'],10);
		add_action('woocommerce_process_product_meta_simple',[$this,'save_new_content'],10,1);
		add_action('admin_enqueue_scripts',function(){wp_enqueue_media();});
		add_action('woocommerce_before_add_to_cart_button',[$this,'show_custom_content'],10);
		add_filter('woocommerce_single_product_image_thumbnail_html', [$this,'remove_featured_image'], 10, 2);
		add_action('woocommerce_single_product_image_thumbnail_html',[$this,'add_custom_product_image'],10);		
		
		add_filter( 'woocommerce_add_cart_item_data', [$this,'validation'], 99, 2 );
		add_action( 'woocommerce_before_calculate_totals', [$this,'calculate'], 99 );
		add_filter( 'woocommerce_get_item_data', [$this,'cart_meta'], 99, 2 );
		add_action( 'woocommerce_add_order_item_meta', [$this,'meta_handler'], 99, 3 );
		
		add_filter('woocommerce_product_price_class',[$this,'product_price_class_change'],10,1);
	}
	
	// Adds customisation tab
	public function add_tab($tabs){
		$tabs['customisation'] = array(
			'title'				 => __('Customisation','woocommerce'),
			'label'	 			 => __('Customisation','woocommerce'),
			'priority'			 => 50,
			'target'			 => 'new_tab',
			'callback'  		 => 'woo_customisation_tab_content'
		);

		return $tabs;
	}
	
	// Populates customised tab
	public function tab_content(){
		global $post;		
		?><div id='new_tab' class='panel woocommerce_options_panel'><?php
		?><div class='options_group'>
			<button type="button" id="addmore" style="position:absolute; left:50%; width: 100px" class="button">Add part</button>
				<span class="wrap">										
					<?php
						$custom_field_type=get_post_meta($post->ID,'__custom_field_type',true);
						$values=get_post_meta($post->ID,'amondi_layers',true);						
						$arr=json_decode($values,true);
						if(empty($arr)){
							?>
							<div id="forma-0" class="clone-form-object">				
							<br></br>		
							<input id="image-url-0" type="text" name="part_details[0][img]" />
							<input id="upload-button-0" type="button" class="button amondi-image-upload" value="Upload Image" />					
							<?php							
								woocommerce_wp_text_input( array(
									'id'				=> '_layer',
									'name'				=> 'part_details[0][layer]',
									'label'				=> __('Layer name','woocommerce'),
									'desc_tip' 			=> 'true',
									'description'		=> __('Enter the layer name.','woocommerce'),
									'type'				=> 'text',
									));
								woocommerce_wp_text_input(array(
									'id'				=> '_part_price',
									'name'				=> 'part_details[0][part_price]',
									'label'				=> __('Part price','woocommerce'),			
									'type'				=> 'number',
									'custom_attributes' => array(
										'step'			=> 'any',
										'min'			=> '0'
									),
									'description'		=> __('Enter the part price','woocommerce'),
									'desc_tip' 			=> 'true',
								)); ?>		
							<input id="remove-button-0" type="button" class="button amondi-remove-part" value="Remove part" />							
							</div>
						<?php
						}
						else{
							foreach($arr as $id=>$arr_part){							
								?>
								<div id="forma-<?php echo $id ?>" class="clone-form-object">				
									<br></br>		
									<input id="image-url-<?php echo $id ?>" value='<?php echo !empty($arr_part['img']) ? $arr_part['img'] : "" ?>' type="text" name="part_details[<?php echo $id ?>][img]" />
									<input id="upload-button-<?php echo $id ?>" type="button" class="button amondi-image-upload" value="Upload Image" />					
									<?php							
									woocommerce_wp_text_input( array(
										'id'				=> '_layer',
										'name'				=> 'part_details['.$id.'][layer]',
										'label'				=> __('Layer name','woocommerce'),
										'desc_tip' 			=> 'true',
										'description'		=> __('Enter the layer name.','woocommerce'),
										'type'				=> 'text',
										'value'				=> $arr_part['layer'],
										));
									woocommerce_wp_text_input(array(
										'id'				=> '_part_price',
										'name'				=> 'part_details['.$id.'][part_price]',
										'label'				=> __('Part price','woocommerce'),			
										'type'				=> 'number',
										'custom_attributes' => array(
											'step'			=> 'any',
											'min'			=> '0'
										),
										'description'		=> __('Enter the part price','woocommerce'),
										'desc_tip' 			=> 'true',
										'value'				=> !empty($arr_part['part_price']) ? $arr_part['part_price'] : "" ,
									));
										?>	
								<input id="remove-button-<?php echo $id ?>" type="button" class="button amondi-remove-part" value="Remove part" />
								<input type="hidden" id="counter">
								</div>	<?php
						}					
					} ?>	
				</span>			
		</div></div><?php		
	}

	// Saves items 
	public function save_new_content($post_id){		
		$titles = $_POST["part_details"];
		
		// Save items here
		update_post_meta($post_id,'amondi_layers', json_encode($titles));	
	}
	
	//Shows buttons for layers
	public function show_custom_content(){
		global $post;
		$product=wc_get_product($post->ID);
		$title=get_post_meta($post->ID,'amondi_layers',true);		
		$arr=json_decode($title,true);
		if($title){
			foreach($arr as $id=>$arr_part){
				$layer=!empty($arr_part['layer']) ? $arr_part['layer'] : "";
				$part_price= !empty($arr_part['part_price']) ? $arr_part['part_price'] : "";
				printf('<div class="custom_field_wrapper">
				<label id="label-'.$id.'" class="button alt" style="display:block;width:150px;""><input type="checkbox" id="selection-'.$id.'" class="amondi-button" name="layer-'.$id.'" value="'.$layer.'" data-price="'.$part_price.'" style="display:none;">'.$layer.'</label>
				</div>',esc_html($title));
			}
			printf("<br></br>");
		}
	}
	
	public function remove_featured_image($html, $attachment_id ) {
		global $post, $product;
		$featured_image = get_post_thumbnail_id( $post->ID );
		if ( $attachment_id == $featured_image )
			$html = '';
		return $html;
	}
	
	public function add_custom_product_image(){
		global $post;
		global $product; ?>
		<img src="<?php echo wp_get_attachment_url( $product->get_image_id() ); ?>" width="400" height="300" style="position: absolute; left: auto; right:auto; top: 50px; bottom: auto;"/><?php
		$products=wc_get_product($post->ID);
		$title=get_post_meta($post->ID,'amondi_layers',true);		
		$arr=json_decode($title,true);
		if($title){
			foreach($arr as $id=>$arr_part){
				$img=!empty($arr_part['img']) ? $arr_part['img'] : "";
				printf('<div class="custom_image_wrapper">
				<img id="imgs-'.$id.'" width="400px" height="300px" src="'.$img.'" style="position: absolute; left: auto; right:auto; top: 50px; bottom: auto; display: none;">
				</div>',esc_html($title));
			}
		}
	}

	public function validation($cart_item_data,$product_id){
		$title=get_post_meta($product_id,'amondi_layers',true);	
		$arr=json_decode($title,true);
		if($title){
			foreach($arr as $id=>$arr_part){
				if(isset($_POST['layer-'.$id]) && $_POST['layer-'.$id]===$arr_part['layer']){
					$cart_item_data['layers'][$arr_part['layer']]=$arr_part['part_price'];
				}
			}
			return $cart_item_data;
		}	
	}
	
	public function calculate($cart_object){
		if(!WC()->session->__isset("reload_checkout")){
			foreach(WC()->cart->get_cart() as $key=>$value){
				if(isset($value['layers'])){
					foreach($value['layers'] as $id=>$layer){
						if(method_exists($value['data'],"set_price")){
							$orgPrice=floatval($value['data']->get_price());
							$value['data']->set_price($orgPrice+$layer);
							$value['data']->set_sale_price($orgPrice+$layer);
						}
					}
				}
			}
		}
	}
	
	public function cart_meta($cart_data,$cart_item=null){
		$meta_items=array();
		if(!empty($cart_data)){
			$meta_items=$cart_data;
		}
		if(isset($cart_item['layers'])){
			foreach($cart_item['layers'] as $id=>$layer){			
				$meta_items[]=array("name"=>$id, "value"=>get_woocommerce_currency_symbol()."".$layer);
			}
		}
		return $meta_items;
	}
	
	public function meta_handler($item_id,$values,$cart_item_key){
		if(isset($values['layers'])){
			foreach($values['layers'] as $id=>$layer){	
				wc_add_order_item_meta($item_id,$id,get_woocommerce_currency_symbol()."".$layer);
			}
		}
	}
	
	public function product_price_class_change($value){
		return $value . " amondi-price";
	}
}
