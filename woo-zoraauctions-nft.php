<?php

	/**
	 * Plugin Name: ART HAUS NFT for WooCommerce
	 * Plugin URI: https://art.haus
	 * Description: WooCommerce plugin for NFT auctions using zora
	 * Version: 1.2.0
	 * Author: ART HAUS
	 * Author URI: https://art.haus
	 * Text domain: arthaus_nft_plugin
	 */


	define( 'ABSZAN_TEXT_DOMAIN', 'arthaus_nft_plugin');
	define( 'ABSZAN_VERSION', '1.2.0' ); // WRCS: DEFINED_VERSION.
	define( 'ABSZAN_FILE', __FILE__ );
	define( 'ABSZAN_URL', plugins_url( '', __FILE__ ) );
	define( 'ABSZAN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'ABSZAN_INC_DIR', ABSZAN_DIR . 'includes' );
	define( 'ABSZAN_INC_URL', ABSZAN_URL . '/includes' );

	define( 'ABSZAN_ASSETS_URL', ABSZAN_URL . '/assets' );

	define( 'ABSZAN_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

	// echo ABSZAN_ASSETS_URL;

	require_once ABSZAN_DIR . '/init.php';
	require_once ABSZAN_DIR . '/network-options.php';