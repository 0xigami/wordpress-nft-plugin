<?php
/**
 * Plugin Name: 	WooCommerce Custom Product Type
 * Plugin URI:		http://jeroensormani.com
 * Description:		A simple demo plugin on how to add a custom product type.
 */

/**
 * Register the custom product type after init
 */
function register_nft_auction_product_type() {

	/**
	 * This should be in its own separate file.
	 */
	class WC_Product_NFT_Auction extends WC_Product {

		public function __construct( $product ) {

			// 
			add_option( 'nft_rinkeby_api_url', 'https://rinkeby.infura.io/v3/bd80c5f7a9e4449f958ef1e3910111ba', '', 'yes' );

			$this->product_type = 'nft_auction';

			parent::__construct( $product );

		}

	}

}
add_action( 'plugins_loaded', 'register_nft_auction_product_type' );


add_action( 'wp_enqueue_scripts', 'nf_auction_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'nf_auction_enqueue_backend_scripts' );


function nf_auction_enqueue_backend_scripts(){
	wp_enqueue_style( 'flatpickr_date_picker_css', ABSZAN_ASSETS_URL . '/js/flatpickr.min.css' );

	wp_enqueue_script( 'abszan_web3_min', ABSZAN_ASSETS_URL . '/js/web3.min.js', array(), false, false );
	wp_enqueue_script( 'abszan_web3modal_min', ABSZAN_ASSETS_URL . '/js/web3modal.js', array(), false, false );
	wp_enqueue_script( 'abszan_evm_min', ABSZAN_ASSETS_URL . '/js/evmchain.js', array(), false, false );
	wp_enqueue_script( 'abszan_walletconnect_min', ABSZAN_ASSETS_URL . '/js/walletconnect.js', array(), false, false );
	wp_enqueue_script( 'abszan_abis', ABSZAN_ASSETS_URL . '/js/abis.js', array(), false, false );
	wp_enqueue_script( 'abszan_data', ABSZAN_ASSETS_URL . '/js/data.js', array(), false, false );
	wp_enqueue_script( 'abszan_networks', ABSZAN_ASSETS_URL . '/js/networks.js', array(), false, false );
	wp_enqueue_script( 'abszan_block_script', ABSZAN_ASSETS_URL . '/js/Block.js', array(), false, false );
	wp_localize_script( 'abszan_block_script', 'nftData',
	        array( 
	            'network_options' => get_option( 'nft_network_options' )
	        )
	    );

	wp_enqueue_script( 'flatpickr_date_picker_js', ABSZAN_ASSETS_URL . '/js/flatpickr.js', array(), false, false );

	wp_enqueue_script( 'jquery_duration_picker_js', ABSZAN_ASSETS_URL . '/js/jquery.durationpicker.min.js', array(), false, false );
}

function nf_auction_enqueue_scripts(){

	wp_enqueue_script( 'abszan_web3_min', ABSZAN_ASSETS_URL . '/js/web3.min.js', array(), false, false );
	wp_enqueue_script( 'abszan_web3modal_min', ABSZAN_ASSETS_URL . '/js/web3modal.js', array(), false, false );
	wp_enqueue_script( 'abszan_evm_min', ABSZAN_ASSETS_URL . '/js/evmchain.js', array(), false, false );
	wp_enqueue_script( 'abszan_walletconnect_min', ABSZAN_ASSETS_URL . '/js/walletconnect.js', array(), false, false );
	wp_enqueue_script( 'abszan_abis', ABSZAN_ASSETS_URL . '/js/abis.js', array(), false, false );
	wp_enqueue_script( 'abszan_data', ABSZAN_ASSETS_URL . '/js/data.js', array(), false, false );
	wp_enqueue_script( 'abszan_networks', ABSZAN_ASSETS_URL . '/js/networks.js', array(), false, false );
	wp_enqueue_script( 'abszan_block_script', ABSZAN_ASSETS_URL . '/js/Block.js', array(), false, false );

	if( is_product() || is_shop() ){
		global $post;
		
		$nft_network_options = get_option( 'nft_network_options' );

		$selectedNetworkKey = null;

		foreach( $nft_network_options as $key => $network ){
			if( $network['chain_id'] == get_post_meta( $post->ID ,'_token_network', true ) ){
				$selectedNetworkKey = $key;
				break;
			}
		}

		wp_enqueue_script( 'fro_nft_product', ABSZAN_ASSETS_URL . '/js/nft-product.js', array(), false, true );
	    wp_localize_script( 'fro_nft_product', 'nftData',
	        array( 
	            'token' => get_post_meta( $post->ID, '_token_id', true ),
	            'tokenContract' => get_post_meta( $post->ID, '_token_contract', true ),
	            'auction' => get_post_meta( $post->ID, '_auction_id', true ),
	            'block' => get_post_meta( $post->ID, '_block_number', true ),
	            // 'rpc' => get_option('nft_rinkeby_api_url', false),
	            'network_option' => $nft_network_options[$selectedNetworkKey]
	        )
	    );
	    wp_enqueue_script( 'abszan_web3modal_script', ABSZAN_ASSETS_URL . '/js/web3modal.js', array(), false, false );
	    wp_enqueue_script( 'abszan_evmchain_script', ABSZAN_ASSETS_URL . '/js/evmchain.js', array(), false, false );
	    wp_enqueue_script( 'abszan_walletconnect_script', ABSZAN_ASSETS_URL . '/js/walletconnect.js', array(), false, false );
	}
	
}

/**
 * Add to product type drop down.
 */
function add_nft_auction_product( $types ){

	// Key should be exactly the same as in the class
	$types[ 'nft_auction' ] = __( 'NFT Auction' );

	return $types;

}
add_filter( 'product_type_selector', 'add_nft_auction_product' );


/**
 * Show pricing fields for nft_auction product.
 */
function nft_auction_custom_js() {

	if ( 'product' != get_post_type() ) :
		return;
	endif;

	?><script type='text/javascript'>
		jQuery( document ).ready( function() {
			jQuery( '.options_group.pricing' ).addClass( 'show_if_nft_auction' ).show();
		});

	</script><?php

}
add_action( 'admin_footer', 'nft_auction_custom_js' );


/**
 * Add a custom product tab.
 */
function custom_product_tabs( $tabs) {

	$tabs['nft'] = array(
		'label'		=> __( 'NFT Auction', 'woocommerce' ),
		'target'	=> 'nft_auction_options',
		'class'		=> array( 'show_if_nft_auction'  ),
	);

	return $tabs;

}
add_filter( 'woocommerce_product_data_tabs', 'custom_product_tabs' );


/**
 * Contents of the nft options product tab.
 */
function nft_auction_options_product_tab_content() {

	global $post;

	?><div id='nft_auction_options' class='panel woocommerce_options_panel'><?php

		?><div class='options_group'><?php

			$options = [];

			$nft_network_options = get_option( 'nft_network_options' );
			if( $nft_network_options && is_array( $nft_network_options ) ){
				foreach( $nft_network_options as $key => $network ){
					$options[$network['chain_id']] = $network['network_name'];
				}
			}

			woocommerce_wp_select(
				array(
			        'id'      => '_token_network',
			        'label'   => __( 'Network Options', ABSZAN_TEXT_DOMAIN ),
			        'options' =>  $options, //this is where I am having trouble
			        'value'   => get_post_meta( $post->ID ,'_token_network', true ),
		    	)
			);

			woocommerce_wp_text_input(
				array(
					'id'          => '_token_id',
					'label'       => __( 'Token ID', ABSZAN_TEXT_DOMAIN ),
					'description' => __( '(Number)', ABSZAN_TEXT_DOMAIN ),
					'value'       => get_post_meta( $post->ID ,'_token_id', true ),
					'default'     => '',
					'placeholder' => '',
				)
			);

			woocommerce_wp_text_input(
				array(
					'id'          => '_token_contract',
					'label'       => __( 'Token Contract', ABSZAN_TEXT_DOMAIN ),
					'description' => __( '(Contract Address)', ABSZAN_TEXT_DOMAIN ),
					'value'       => get_post_meta( $post->ID ,'_token_contract', true ),
					'default'     => '',
					'placeholder' => '',
				)
			);

			woocommerce_wp_text_input(
					array(
						'id'          => '_auction_duration_picker',
						'type'		  => 'number',
						'label'       => __( '', ABSZAN_TEXT_DOMAIN ),
						'description' => __( '<span id="_auction_duration_picker_span">Select A Value</span>', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_auction_duration_picker', true ),
						'default'     => '',
						'placeholder' => '',
					)
				);

				// woocommerce_wp_text_input(
				// 	array(
				// 		'id'          => '_auction_duration_day',
				// 		'type'		  => 'number',
				// 		'label'       => __( 'Auction Day', ABSZAN_TEXT_DOMAIN ),
				// 		'description' => __( '(day)', ABSZAN_TEXT_DOMAIN ),
				// 		'value'       => get_post_meta( $post->ID ,'_auction_duration_day', true ),
				// 		'default'     => '',
				// 		'placeholder' => '',
				// 	)
				// );

				// woocommerce_wp_text_input(
				// 	array(
				// 		'id'          => '_auction_duration_hours',
				// 		'type'		  => 'number',
				// 		'label'       => __( 'Auction Hours', ABSZAN_TEXT_DOMAIN ),
				// 		'description' => __( '(hours)', ABSZAN_TEXT_DOMAIN ),
				// 		'value'       => get_post_meta( $post->ID ,'_auction_duration_hours', true ),
				// 		'default'     => '',
				// 		'placeholder' => '',
				// 	)
				// );

				// woocommerce_wp_text_input(
				// 	array(
				// 		'id'          => '_auction_duration_minutes',
				// 		'type'		  => 'number',
				// 		'label'       => __( 'Auction Minutes', ABSZAN_TEXT_DOMAIN ),
				// 		'description' => __( '(minutes)', ABSZAN_TEXT_DOMAIN ),
				// 		'value'       => get_post_meta( $post->ID ,'_auction_duration_minutes', true ),
				// 		'default'     => '',
				// 		'placeholder' => '',
				// 	)
				// );

				// woocommerce_wp_text_input(
				// 	array(
				// 		'id'          => '_auction_duration_seconds',
				// 		'type'		  => 'number',
				// 		'label'       => __( 'Auction Seconds', ABSZAN_TEXT_DOMAIN ),
				// 		'description' => __( '(seconds)', ABSZAN_TEXT_DOMAIN ),
				// 		'value'       => get_post_meta( $post->ID ,'_auction_duration_seconds', true ),
				// 		'default'     => '',
				// 		'placeholder' => '',
				// 	)
				// );			

			// woocommerce_wp_text_input(
			// 		array(
			// 			'id'          => '_auction_duration',
			// 			'type'		  => 'datetime-local',
			// 			'label'       => __( 'Auction Duration', ABSZAN_TEXT_DOMAIN ),
			// 			'description' => __( '(The length of time, in seconds, that the auction should run for once the reserve price is hit.)', ABSZAN_TEXT_DOMAIN ),
			// 			'value'       => get_post_meta( $post->ID ,'_auction_duration', true ),
			// 			'default'     => '',
			// 			'placeholder' => '',
			// 		)
			// 	);

				woocommerce_wp_text_input(
					array(
						'id'          => '_reserve_price',
						'label'       => __( 'Reserve Price', ABSZAN_TEXT_DOMAIN ),
						'description' => __( '(The minimum price for the first bid, starting the auction)', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_reserve_price', true),
						'default'     => '',
						'placeholder' => '',
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_curator',
						'label'       => __( 'Curator', ABSZAN_TEXT_DOMAIN ),
						'description' => __( '(Ethereum Address Optional)', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_curator', true),
						'default'     => '',
						'placeholder' => '',
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_owner',
						'label'       => __( 'Owner', ABSZAN_TEXT_DOMAIN ),
						'description' => __( '', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_owner', true),
						'default'     => '',
						'placeholder' => '',
						'custom_attributes' => array(
							'readonly' => 'readonly'
						)
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_curator_fee_percent',
						'type'		  => 'number',
						'label'       => __( 'Curator Free Percentage', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_curator_fee_percent', true),
						'default'     => '',
						'placeholder' => '',
						'custom_attributes' => array(
							'step' 	=> 'any',
							'min'	=> '0'
						)
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_auction_currency',
						'label'       => __( 'Auction Currency', ABSZAN_TEXT_DOMAIN ),
						'description' => __( '(The currency to perform this auction in, or 0x0 for ETH)', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_auction_currency', true),
						'default'     => '',
						'placeholder' => '',
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_auction_id',
						'label'       => __( 'Auction Id', ABSZAN_TEXT_DOMAIN ),
						'description' => __( '', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_auction_id', true),
						'default'     => '',
						'placeholder' => '',
						'custom_attributes' => array(
							'readonly' => 'readonly'
						)
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_block_number',
						'label'       => __( 'Block Number', ABSZAN_TEXT_DOMAIN ),
						'description' => __( '', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_block_number', true),
						'default'     => '',
						'placeholder' => '',
						'custom_attributes' => array(
							'readonly' => 'readonly'
						)
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => '_auction_status',
						'label'       => __( 'Auction Status', ABSZAN_TEXT_DOMAIN ),
						'description' => __( '', ABSZAN_TEXT_DOMAIN ),
						'value'       => get_post_meta( $post->ID ,'_auction_status', true),
						'default'     => '',
						'placeholder' => '',
						'custom_attributes' => array(
							'readonly' => 'readonly'
						)
					)
				);

			?>
			<p class="form-field">
				<div id="_auction_popup" class="_auction_popup overlay">
					<div class="_auction_popup_inner">
						<p class="_popup_description"></p>
						<h1 class="_popup_network_name">Network Name</h1>
					</div>
				</div>
				
				<?php 
	  				if( get_post_meta( $post->ID, '_auction_status', true ) == 'Cancelled' ){
	  					// do nothing
	  				}else{
	  					?>
	  					<label for=""></label>
						<a href="javascript:void(0);" class="button button-primary button-large" id="connect-btn">Connect Wallet</a>

						<?php
	  					
	  					$auctionID = get_post_meta( $post->ID, '_auction_id', true );
		  				if( $auctionID !== '' && !is_null($auctionID) && isset($auctionID) ){
		  					?>
		  						<a href="javascript:void(0);" class="button button-primary button-large" id="auction-cancel">Cancel Auction</a>
		  					<?php
		  				}else{
		  					?>
		  						<a href="javascript:void(0);" class="button button-primary button-large" id="approve" style="display: none;">Approve</a>
		  						<a href="javascript:void(0);" class="button button-primary button-large" id="auction-create" style="display:none;">Create Auction Contract</a>
		  					<?php
		  				}
	  				}
	  			?>
	  			<style>
	  				.duration-picker-container{
					  font-size: 14px;
					}

					.duration-picker-container select{
					  width: 45px;
					  display: inline-block;
					  height: 26px;
					  padding: 0;
					  box-sizing: content-box;
					  border-radius: 3px;
					  margin-left: 10px;
					  background: #fff;
					  border: 1px solid #e1e1e1;
					  font-size: 13px;
					}
					.overlay {
						position: fixed;
						top: 0;
						bottom: 0;
						left: 0;
						right: 0;
						background: rgba(0, 0, 0, 0.7);
						transition: opacity 500ms;
						visibility: hidden;
						opacity: 0;
					}
					._auction_popup_inner{
						margin: 70px auto;
						padding: 20px;background: #fff;border-radius: 5px;width: 30%;position: relative;transition: all 5s ease-in-out;
					}
	  			</style>
	  			<script>
	  				flatpickr("#_auction_duration", {
	  					enableTime: true,
	  					dateFormat: "Y-m-d H:i",
	  				});

	  				jQuery('input[name=_auction_duration_picker]').durationPicker()
					.on("change", function(jQueryval){
						jQuery('#_auction_duration_picker_span').text("Duration (secs): " + jQuery(this).val());
					});
	  			</script>
			</p>
			

		</div>

	</div><?php


}
add_action( 'woocommerce_product_data_panels', 'nft_auction_options_product_tab_content' );


/**
 * Save the custom fields.
 */
function save_nft_auction_field( $post_id ) {

	$_token_network = isset( $_POST['_token_network'] ) ? sanitize_text_field( $_POST['_token_network'] ) : '';
	$_token_id = isset( $_POST['_token_id'] ) ? sanitize_text_field( $_POST['_token_id'] ) : '';
	$_token_contract = isset( $_POST['_token_contract'] ) ? sanitize_text_field( $_POST['_token_contract'] ) : '';
	$_auction_duration = isset( $_POST['_auction_duration'] ) ? sanitize_text_field( $_POST['_auction_duration'] ) : '';

	$_auction_duration_day = isset( $_POST['_auction_duration_day'] ) ? sanitize_text_field( $_POST['_auction_duration_day'] ) : '';
	$_auction_duration_hours = isset( $_POST['_auction_duration_hours'] ) ? sanitize_text_field( $_POST['_auction_duration_hours'] ) : '';
	$_auction_duration_minutes = isset( $_POST['_auction_duration_minutes'] ) ? sanitize_text_field( $_POST['_auction_duration_minutes'] ) : '';
	$_auction_duration_seconds = isset( $_POST['_auction_duration_seconds'] ) ? sanitize_text_field( $_POST['_auction_duration_seconds'] ) : '';

	$_auction_duration_picker = isset( $_POST['_auction_duration_picker'] ) ? sanitize_text_field( $_POST['_auction_duration_picker'] ) : '';

	$_reserve_price = isset( $_POST['_reserve_price'] ) ? sanitize_text_field( $_POST['_reserve_price'] ) : '';
	$_curator = isset( $_POST['_curator'] ) ? sanitize_text_field( $_POST['_curator'] ) : '';
	$_curator_fee_percent = isset( $_POST['_curator_fee_percent'] ) ? sanitize_text_field( $_POST['_curator_fee_percent'] ) : '';
	$_auction_currency = isset( $_POST['_auction_currency'] ) ? sanitize_text_field( $_POST['_auction_currency'] ) : '';
	$_auction_id = isset( $_POST['_auction_id'] ) ? sanitize_text_field( $_POST['_auction_id'] ) : '';
	$_block_number = isset( $_POST['_block_number'] ) ? sanitize_text_field( $_POST['_block_number'] ) : '';
	$_owner = isset( $_POST['_owner'] ) ? sanitize_text_field( $_POST['_owner'] ) : '';
	$_auction_status = isset( $_POST['_auction_status'] ) ? sanitize_text_field( $_POST['_auction_status'] ) : '';
	

	update_post_meta( $post_id, '_token_network', $_token_network );
	update_post_meta( $post_id, '_token_id', $_token_id );
	update_post_meta( $post_id, '_token_contract', $_token_contract );
	update_post_meta( $post_id, '_auction_duration', $_auction_duration );

	update_post_meta( $post_id, '_auction_duration_picker', $_auction_duration_picker );

	update_post_meta( $post_id, '_auction_duration_day', $_auction_duration_day );
	update_post_meta( $post_id, '_auction_duration_hours', $_auction_duration_hours );
	update_post_meta( $post_id, '_auction_duration_minutes', $_auction_duration_minutes );
	update_post_meta( $post_id, '_auction_duration_seconds', $_auction_duration_seconds );

	update_post_meta( $post_id, '_reserve_price', $_reserve_price );
	update_post_meta( $post_id, '_curator', $_curator );
	update_post_meta( $post_id, '_curator_fee_percent', $_curator_fee_percent );
	update_post_meta( $post_id, '_auction_currency', $_auction_currency );
	update_post_meta( $post_id, '_auction_id', $_auction_id );
	update_post_meta( $post_id, '_block_number', $_block_number );
	update_post_meta( $post_id, '_owner', $_owner );
	update_post_meta( $post_id, '_auction_status', $_auction_status );

}
add_action( 'woocommerce_process_product_meta_nft_auction', 'save_nft_auction_field'  );


function nft_auction_product_front(){
	
	global $product;
	
	if( 'nft_auction' == $product->get_type() ){
		
		?>
			<div>
				<style>
					.product-type-nft_auction{
						padding: 4rem 0;
					}
					.product-type-nft_auction .product_meta,
					.product-type-nft_auction .woocommerce-breadcrumb{
						display: none !important;
						visibility: hidden !important;
						height: 0 !important;
					}
					.product-type-nft_auction .product_title{
						text-transform: capitalize;
					}
					.product-type-nft_auction #_auction_amount{
						border-radius: 9999px;
						padding-left: 20px;
						padding-right: 20px;
						padding-top: 1rem;
						padding-bottom: 1rem;
					}
					.woocommerce-product-gallery{
						border-radius: 6px;
						overflow: hidden;
					}
					.__auction_bid_form .input-wrap{
						display: block;
						width: 100%;
						margin-bottom: 12px;
					}
					.input-wrap input{
						width: 100%;
					}
					.input-wrap .description{
						display: inline-block;
						font-size: small;
					}
					.__auction_bid_form ._btn_place_bid{
						display: inline-block;
						padding: 12px 15px;
						width: 100%;
						cursor: pointer;
						background-color: #222;
						color: #fff;
						text-transform: uppercase;
						text-align: center;
						border-radius: 9999px;
						font-weight: 500;
					}
					.__auction_bid_form ._btn_place_bid:hover{
						background-color: #000;
					}
					.__auction_bid_form .__details{
						margin-bottom: 11px;
					}
					.__auction_bid_form .__details h2{
						font-size: 17px;
						font-weight: 600;
					}
					._auction_timing{
						margin-top: 15px;
					}
					._auction_timing h4{
						margin-top: 14px;
						/*margin-bottom: 14px;*/
						font-size: 17px;
					}
					._auction_timing_in{
						display: flex;
						align-items: center;
						margin-bottom: 15px;
					}
					._auction_timing_in > span{
						/*display: flex;
						width: 75px;
						height: 75px;
						border-radius: 50%;
						border:  solid 4px #333;
						align-items: center;
						justify-content: center;
						line-height: 1;
						text-align: center;*/
						font-weight: 600;
						font-size: ;
					}
					._auction_timing_in > span:not(:last-child){
						margin-right: 10px;
					}
					._last_bidding{
						/*border-bottom: 1px solid rgba(0,0,0,.05);
						border-top: 1px solid rgba(0,0,0,.05);
						*/padding-top: .56rem;
						padding-bottom: .56rem;
					}
					._last_bidding #_bidder{
						font-size: 1.3rem;
						word-break: break-all;
						margin-bottom: .56em;
					}
					._highest_bid{
						margin-bottom: 9px;
					}
					._last_bidding #_bidder_amount,
					._highest_bid ._bidder_amount
					{
						font-size: 15px;
						font-weight: bold;
					}
					._auction_reserve_price{
						border-top: solid 1px #222;
						margin-bottom: 10px;
						padding-bottom: 10px;
					}
					._action_history{
						border-top: 1px solid rgba(0,0,0,.05);
						margin-top: 18px;
						padding-top: 12px;
					}
					._action_history h4{
						margin-bottom: 13px;
					}
					._action_history ul{
						margin: 0 0 0 29px;
						padding: 0;
					}
					._action_history li{
						margin-bottom: 6px;
						color: #333;
					}
					._action_history li a{
						color: #333;
						text-decoration: none;
					}
					._action_history li a:hover{
						text-decoration: underline;
					}
					.overlay {
						position: fixed;
						top: 0;
						bottom: 0;
						left: 0;
						right: 0;
						background: rgba(0, 0, 0, 0.7);
						transition: opacity 500ms;
						visibility: hidden;
						opacity: 0;
					}
					._auction_popup_inner{
						margin: 70px auto;
						padding: 20px;background: #fff;border-radius: 5px;width: 30%;position: relative;transition: all 5s ease-in-out;
					}
					#_auction_amount_desc{
						display: block;
						text-align: center;
						font-weight: 500;
						text-align: center;
					}
					._network_error{
						display: block;
						background-color: #ff0000;
						color: #fff;
						text-align: center;
						font-weight: 500;
						padding: .5rem .7rem;
						margin-bottom: .7rem;
						border-radius: 6px;
					}
				</style>
				<div id="_auction_popup" class="_auction_popup overlay">
					<div class="_auction_popup_inner">
						<p class="_popup_description"></p>
						<h1 class="_popup_network_name">Network Name</h1>
					</div>
				</div>
				<form action="" class="__auction_bid_form">
					<div id="_auction_timing" class="_auction_timing" style="display: none">
					
						<h4 style="font-weight: 600"><?php _e('Time left') ?></h4>
						<?php
							$now = new DateTime();
					    	$then = new DateTime(get_post_meta( $product->get_id(), '_auction_duration', true ));
					    	$diff = $now->diff($then);
					    	//echo $diff->format('%d days %h hours %i minutes %s seconds');
				    	?>	
				    	<div class="_auction_timing_in">
				    		<span id="_day"><?php echo $diff->format('%d <br />Days'); ?></span>
				    		<span id="_hours"><?php echo $diff->format('%h <br />Hours'); ?></span>
				    		<span id="_mins"><?php echo $diff->format('%i <br />Mins'); ?></span>
				    		<span id="_secs"><?php echo $diff->format('%s <br />Secs'); ?></span>
				    	</div>
				    	
				    	<h4 style="font-weight: 600"><?php _e('Highest bid') ?></h4>
				    	<div class="_highest_bid">
							<h3 class="_bidder_amount">0.0042 ETH</h3>
						</div>

				    	<!-- <h4 style="font-weight: 600" style="display: none !important"><?php _e('Last bid') ?></h4> -->
				    	<div class="_last_bidding" style="display: none">
							<h2 id="_bidder">Text Here</h2>
							<h3 id="_bidder_amount">Text Here</h3>
						</div>

					</div>
					<div class="__details" style="display: none">
						<h2><?php _e('Auction ID'); ?>: <?php echo get_post_meta( $product->get_id(), '_auction_id', true ); ?></h2>
					</div>
					<div class="__details" style="display: none">
						<h2 id="_auction_owned_by"><?php _e('Owned by'); ?>: <a href="#" target="_blank" id="__token__owner"></a></h2>
					</div>
					<div class="input-wrap">
						<input type="text" id="_auction_amount" name="_auction_amount" placeholder="Amount (unit256)">
					</div>
					<div class="input-wrap button-wrap">

						<h2 id="_auction_not_approved" style="display: none;">Auction Not Approved</h2>
						
						<a id="_auction_fr_approve"  class="_btn_place_bid" style="display: none;" href="javascript:void(0);" data-token="<?php echo get_post_meta( $product->get_id(), '_token_id', true ); ?>" data-tokecontract="<?php echo get_post_meta( $product->get_id(), '_token_contract', true ); ?>">Approve</a>


						<a id="_auction_fr_connect"  class="_btn_place_bid" href="javascript:void(0);" data-token="<?php echo get_post_meta( $product->get_id(), '_token_id', true ); ?>" data-tokecontract="<?php echo get_post_meta( $product->get_id(), '_token_contract', true ); ?>">Connect</a>
						<a id="_auction_fr_place_bid"  style="display:none" class="_btn_place_bid" href="javascript:void(0);" data-token="<?php echo get_post_meta( $product->get_id(), '_token_id', true ); ?>" data-tokecontract="<?php echo get_post_meta( $product->get_id(), '_token_contract', true ); ?>">Place Bid</a>
						<a id="_auction_fr_end_auction" style="display:none" class="_btn_place_bid" href="javascript:void(0);" data-token="<?php echo get_post_meta( $product->get_id(), '_token_id', true ); ?>" data-tokecontract="<?php echo get_post_meta( $product->get_id(), '_token_contract', true ); ?>">End Auction</a>
					</div>
					<span id="_network_error" class="_network_error">There is an issue with the network selection</span>
					<span id="_auction_amount_desc">The bid must be 5% more than the current bid amount</span>
				</form>
				
				<div id="_auction_reserve_price" class="_auction_reserve_price" style="display: none">
					<h4 style="font-weight: 600"><?php _e('Reserve Price'); ?></h4>
					<h3 style="font-weight: 700; font-size: 150%;">
						<?php echo get_post_meta( $product->get_id(), '_reserve_price', true ); ?>
					</h3>
				</div>
				

				<div id="_action_history" class="_action_history">
					<h4 style="font-weight: 600">Auction ID: <?php echo get_post_meta( $product->get_id(), '_auction_id', true ); ?> (history)</h4>
					<ul>
						<!-- <li>No Bids Available</li> -->
					</ul>
				</div>
			</div>
		<?php
	}

}

add_action( 'woocommerce_single_product_summary', 'nft_auction_product_front' );


remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 9 );

add_filter( 'woocommerce_locate_template', 'nft_auction_woocommerce_locate_template', 10, 3 );

function nft_auction_woocommerce_locate_template( $template, $template_name, $template_path ) {
	global $woocommerce;

	$_template = $template;

	if ( ! $template_path ) $template_path = $woocommerce->template_url;



		$plugin_path  = ABSZAN_PLUGIN_PATH . '/woocommerce/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(

			array(
				$template_path . $template_name,
				$template_name
			)
		);

		// Modification: Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;

			// Use default template
			if ( ! $template )
			$template = $_template;

	// Return what we found
	return $template;
}