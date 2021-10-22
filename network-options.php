<?php
	
	function nft_auction_register_options_page() {
  		add_options_page('Network Options', 'Network Options', 'manage_options', ABSZAN_TEXT_DOMAIN, 'nft_auction_options_page');
	}
	add_action('admin_menu', 'nft_auction_register_options_page');


	function nft_auction_options_page(){
		?>
			<div class="wrap">
				<h2>Network Options</h2>
				<form method="post" name="network_options" id="network_options" action="admin-post.php">
					<table>
						<tr valign="top">
							<th scope="row"><label for="no_chain_id">Chain ID</label></th>
							<td>
								<input type="text" id="no_chain_id" name="no_chain_id" placeholder="Chain ID">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="no_network_name">Network Name</label></th>
							<td>
								<input type="text" id="no_network_name" name="no_network_name" placeholder="Network Name">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="no_rpc_url">RPC Url / Infura ID</label></th>
							<td>
								<input type="text" id="no_rpc_url" name="no_rpc_url" placeholder="RPC Url">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="no_symbol">Symbol</label></th>
							<td>
								<input type="text" id="no_symbol" name="no_symbol" placeholder="Symbol">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="no_contract_address">Contract Address</label></th>
							<td>
								<input type="text" id="no_contract_address" name="no_contract_address" placeholder="Contract Address">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="no_explorer_url">Explorer URL</label></th>
							<td>
								<input type="text" id="no_explorer_url" name="no_explorer_url" placeholder="Explorer URL">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">&nbsp;</th>
							<td>
								<br>
								<input type="submit" name="network_options_submit" value="Add Network Option">
							</td>
						</tr>
					</table>
				</form>
				<?php
					$nft_network_options = get_option( 'nft_network_options' );
					if( $nft_network_options && is_array( $nft_network_options ) ){
						?>
						<table class="wp-list-table widefat fixed striped table-view-list network-options" style="margin-top: 25px;">
							<thead>
								<tr>
									<th class="manage-column">Chain ID</th>
									<th class="manage-column">Network Name</th>
									<th class="manage-column">RPC Url</th>
									<th class="manage-column">Symbol</th>
									<th class="manage-column">Contract Address</th>
									<th class="manage-column">Explorer URL</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach( $nft_network_options as $key => $network ){
										?>
										<tr class="status-publish format-standard hentry" id="network_option_<?php echo $key; ?>">
											<td>
												<?php echo $network['chain_id']; ?>
											</td>
											<td>
												<?php echo $network['network_name']; ?>
											</td>
											<td>
												<?php echo $network['rpc_url']; ?>
											</td>
											<td>
												<?php echo $network['symbol']; ?>
											</td>
											<td>
												<?php echo $network['contract_address']; ?>
											</td>
											<td>
												<?php echo $network['explorer_url']; ?>
											</td>
											<td>
												<form method="post" name="network_options_delete" id="network_options_delete" action="admin-post.php">
													<input type="hidden" name="network_option_key" id="network_option_key_<?php echo $key; ?>" value="<?php echo $key; ?>">
													<button type="submit" name="network_option_delete_submit" style="border:0;background:transparent;width:23px;height:23px;cursor:pointer;padding:0;margin:0;" value="yes"><img src="<?php echo ABSZAN_ASSETS_URL; ?>/img/trash.png" style="max-width:100%" alt="delete" /></button>
												</form>
											</td>
										</tr>
										<?php
									}
								?>
							</tbody>
						</table>
						<?php
					}
				?>
			</div>
		<?php
	}

	add_action( 'admin_init', 'nft_auction_network_options_save' );
	add_action( 'admin_init', 'nft_auction_network_option_delete' );

	function nft_auction_network_options_save(){

		if( !current_user_can('manage_options') ){
			wp_die( esc_html__( 'You do not have permission', ABSZAN_TEXT_DOMAIN ), 'Access Denied', array( 'response' => 403 ) );
		}

		// get post
		$gPost = stripslashes_deep( $_POST );

		if( !empty($gPost['network_options_submit']) && isset($gPost['network_options_submit']) ){

			
			$noChainId = $gPost['no_chain_id'];
			$noNetworkName = $gPost['no_network_name'];
			$noRpcUrl = $gPost['no_rpc_url'];
			$noSymbol = $gPost['no_symbol'];
			$noContractAddress = $gPost['no_contract_address'];
			$noExplorerUrl = $gPost['no_explorer_url'];

			$networkOption = array(
				'chain_id' => $noChainId,
				'network_name' => $noNetworkName,
				'rpc_url' => $noRpcUrl,
				'symbol' => $noSymbol,
				'contract_address' => $noContractAddress,
				'explorer_url' => $noExplorerUrl
			);

			$nft_network_options = get_option( 'nft_network_options' );

			if( $nft_network_options ){
				array_push( $nft_network_options, $networkOption );
			}else{
				$nft_network_options = [];
				array_push( $nft_network_options, $networkOption );
			}

			update_option( 'nft_network_options', $nft_network_options );

			$redirect = admin_url( 'options-general.php?page=arthaus_nft_plugin&update=' . true );
			wp_safe_redirect( $redirect );

			exit;
		}
	}

	function nft_auction_network_option_delete(){
		
		if( !current_user_can('manage_options') ){
			wp_die( esc_html__( 'You do not have permission', ABSZAN_TEXT_DOMAIN ), 'Access Denied', array( 'response' => 403 ) );
		}

		// print_r('Got in delete function');
		// die();

		// // get post
		$gPost = stripslashes_deep( $_POST );

		if( !empty($gPost['network_option_delete_submit']) && isset($gPost['network_option_delete_submit']) ){

			// Get key
			$network_option_key = $gPost['network_option_key'];
			
			// Get all options
			$nft_network_options = get_option( 'nft_network_options' );

			// Unset / delete by key
			unset( $nft_network_options[$network_option_key] );

			// Update option with new array
			update_option( 'nft_network_options', $nft_network_options );

			$redirect = admin_url( 'options-general.php?page=arthaus_nft_plugin&update=' . true );
			wp_safe_redirect( $redirect );

			exit;
		}
	}