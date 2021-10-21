// set current account
currentAccount = null;


function handleAccountsChangedv2(accounts) {
    
    
    if (accounts.length === 0) {
        // console.log('Please connect to MetaMask.');
        
    } else if (accounts[0] !== currentAccount) {

        currentAccount = accounts[0];
        w3 = new Web3(window.ethereum);
        jQuery('#_auction_fr_connect').css({'display' : 'none'});
        if(currentAccount != null) {
            // Set the button label
            //jQuery('#enableMetamask').html(currentAccount)
        }
    }
    
}

function connectHandlerv2() {
    connectv2();
    setInterval(connectv2, 1000);
  }

  async function handleUI(){
  	let resp2 = await GetAuctionDetails2(nftData.auction);
  	GetData();
  	
  	timerUpdate();
  	//console.log(resp2);
  	jQuery("#_auction_owned_by").css("display","block");
  	if (resp2.approved){
  		jQuery("#_auction_fr_approve").css('display','none');
  		jQuery("#_auction_not_approved").css('display','none');
  		let done_resp = await isDone();
		if ( !done_resp && currentAccount){
			jQuery("#_auction_amount").css("display","block");
			jQuery("#_auction_amount_desc").css("display","block");
		}
		  
		  
		  
		  loadHistory();
		  CheckBid();
     	
  	}else{
  		if (currentAccount && currentAccount.toLowerCase() == resp2.curator.toLowerCase()){
  			jQuery("#_auction_fr_approve").css('display','block');

  		}else{

			jQuery("#_auction_fr_approve").css('display','none');
			  if (resp2.tokenOwner == "0x0000000000000000000000000000000000000000"){
				jQuery("#_auction_not_approved").html("Auction Ended");
				jQuery('#_auction_fr_end_auction').css('display','none')
				jQuery("#_auction_owned_by").css("display","none");
				jQuery("#_auction_amount").css("display","none");
				jQuery("#_auction_amount_desc").css("display","none");
				loadHistory();
			  }else{
			  	jQuery("#_auction_fr_connect").html('connect To Approve')
			  }

			  jQuery("#_auction_not_approved").css('display','block');
			  
		  }
  		
  	}
     

  }

  function connectv2() {
    
    ethereum
      .request({ method: "eth_requestAccounts" })
      .then(
      	(accounts) => {
      		handleAccountsChangedv2(accounts);
      		
      		//handleUI();
      	}
      	)
      .catch((err) => {
        if (err.code === 4001) {
          // console.log("Please connect to MetaMask.");
        } else {
          // console.error(err);
        }
      });
  }

// connect wallet

function getNormalizedURI(uri) {
	if (uri.startsWith("ipfs://")) {
	  //return uri.replace("ipfs://", "https://ipfs.io/ipfs/");
	  return uri.replace("ipfs://","https://dweb.link/ipfs/");
	}

	if (uri.startsWith("ipns://")){
		return uri.replace("ipns://","https://dweb.link/ipns/");
	}

	if (uri.startsWith("arweave://")) {
	  return uri.replace("arweave://", "https://arweave.net/");
	}
	return uri;
  }


async function GetData(){
	
	// run code
	let uri = await GetNftUri( nftData.token , nftData.tokenContract );
	let _nft_data_url = getNormalizedURI(uri);
	jQuery.getJSON(_nft_data_url, function( _data ){
		let img_url = getNormalizedURI(_data.image);
		jQuery('#main img.wp-post-image').attr('src', img_url);
		jQuery('#main img.wp-post-image').attr('alt', _data.name);
		let _old_product_name = jQuery('#main .entry-summary .product_title').html();
		jQuery('#main .entry-summary .product_title').html( _data.name );
		
		let _breadCrumb = jQuery('.woocommerce-breadcrumb').html();
		jQuery('.woocommerce-breadcrumb').html( _breadCrumb.replace(_old_product_name, _data.name ) );
	});

	
}


async function GetNFTData($token, $contract) {
	// body...
	let __url = await GetNftUri( $token , $contract );
	return __url;
}

function ShopProducts(){
	console.log("called");
	if( jQuery('ul.products').length > 0 ){
		jQuery('ul.products > li').each(function(){
			let __this = jQuery(this);
			console.log("reached");
			GetNFTData( jQuery(__this).data('token'), jQuery(__this).data('tokencontract') ).then( function( _url ) {
				console.log( _url );
				console.log("setting");
				jQuery.getJSON( getNormalizedURI(_url), function( _data ){
					if( jQuery(__this).find('img').length ){
						let __img = jQuery(__this).find('img');
						jQuery(__img).attr('src', getNormalizedURI(_data.image));
						jQuery(__img).attr('srcset', '');
					}
					if( jQuery(__this).find('.woocommerce-loop-product__title').length ){
						let __title = jQuery(__this).find('.woocommerce-loop-product__title');
						// console.log(_data.name);
						jQuery(__title).html(_data.name);
					}
				});
			} );			
		});
	}
}

async function isDone(){
	let resp2 = await GetAuctionDetails2(nftData.auction);
	let d1 = new Date((Number(resp2.duration) + Number(resp2.firstBidTime)) * 1000);
	time = timeBetweenDates(d1,resp2);
    let cond  = (JSON.stringify(time) == JSON.stringify([0,0,0,0]) && resp2.tokenOwner != "0x0000000000000000000000000000000000000000") && resp2.firstBidTime != "0" ||  resp2.firstBidTime == "0" && resp2.tokenOwner == "0x0000000000000000000000000000000000000000"
	return cond

}

async function CheckBid(){
	let aunctionDetails = await GetAuctionDetails2( nftData.auction );
	//jQuery('#_auction_timing').css({ 'display': 'block' });
	
	if( aunctionDetails.firstBidTime === '0' && currentAccount != null ){
		// show auction price
		jQuery('#_auction_reserve_price h3').html(jQuery('#_auction_reserve_price h3').text().split(' ')[0] + " ETH")
		jQuery('#_auction_reserve_price').css({ 'display': 'block' });
		console.log("block here")
		jQuery('#_auction_fr_place_bid').css({'display' : 'block'});
		
	}else if (currentAccount == null){
		let done_resp = await isDone();
		if (done_resp){
			jQuery("#_auction_fr_connect").html("Connect To End Auction")
		}else{
			jQuery("#_auction_fr_connect").html("Connect To place Bid")
		}
		
	}else{
		let done_resp = await isDone();
		if (!done_resp){
			jQuery('#_auction_fr_place_bid').css({'display' : 'block'});
		}
	}
}


function getW(){
	if (currentAccount){
		return w3;
	}else{
		let options = nftData.network_option;
		let rpc = "";
		if (!([1,3,4,5].includes(Number(options.chain_id)))){
			rpc = options.rpc_url;
		}else{
			rpc = "https://rinkeby.infura.io/v3/" + options.rpc_url;
		}
		let w = new Web3(rpc);
		return w;
	}
}

function loadContract2(abi,add){
        let c = new w3.eth.Contract(abi,add);
        return c;
    }

	function loadContract3(abi, add) {
		let options = nftData.network_option;
		let rpc = "";
		if (!([1,3,4,5].includes(Number(options.chain_id)))){
			rpc = options.rpc_url;
		}else{
			rpc = "https://rinkeby.infura.io/v3/" + options.rpc_url;
		}
		let w = new Web3(rpc);
		let c = new w.eth.Contract(abi, add);
		return c;
	  }
	  
	  async function GetNftUri(NftId, NftC) {
		let c;
		if (currentAccount) {
		  c = loadContract2(NftAbi, NftC);
		} else {
		  c = loadContract3(NftAbi, NftC);
		}
	  
		let resp = await c.methods.tokenURI(NftId).call();
		return resp;
	  }

async function GetAuctionDetails2(auctionId){
	let c;
	if (currentAccount){
		c = loadContract2(HouseAbi,nftData.network_option.contract_address);
	}else{
		c = loadContract3(HouseAbi,nftData.network_option.contract_address);
	}
    let resp = await c.methods.auctions(auctionId).call();
    return resp;
}

function createBid(auctionId,amount){
	// console.log( auctionId + '::' + amount );
	let c = loadContract2(HouseAbi,nftData.network_option.contract_address);
	c.methods.createBid(auctionId,Web3.utils.toWei(String(amount),'ether')).send(
	{
		from:currentAccount,
		value: Web3.utils.toWei(String(amount),'ether')
	}
	).then(
		(res) => {
			alert('created bid');
			loadHistory();
			CheckBid();
		}
	).catch(
		error => {
			console.log(error);
			alert('failed bid');
		}
	)
}

async function loadHistory(){
	let c;
	if (currentAccount){
		c = loadContract2(HouseAbi,nftData.network_option.contract_address);
	}else{
		c = loadContract3(HouseAbi,nftData.network_option.contract_address);
	}
	let startBlock = nftData.block;
	let endBlock = 0;
	let w = getW();
	let latest = await w.eth.getBlockNumber();
	let steps = Math.ceil((latest - startBlock) / 5000);
	let res = [];
	for (let i = 0 ; i < steps ; i++){
		if (endBlock + 5000 >= latest){
            endBlock = latest
        }else{
            endBlock = startBlock + 5000
        }
		let resp = await c.getPastEvents('AuctionBid', {fromBlock:startBlock, toBlock : endBlock,filter : {
			auctionId : nftData.auction
		}});
		for (let j = 0 ; j < resp.length ; j++){
            res.push(resp[j]);
        }
		startBlock = endBlock;
	}
	
	let _liHtml = '';
		// console.log( res );
		let tempres = res.reverse();
		if( tempres.length ){
			tempres.forEach( function( _elem, _index ) {
				_liHtml += '<li> <a href="'+nftData.network_option.explorer_url+_elem.transactionHash+'">' + _elem.returnValues.sender + '</a> : ' + 
				Web3.utils.fromWei(_elem.returnValues.value,'ether')  + '</li>';
			} )
		}else{
			_liHtml = 'No Bids Available';
		}
		console.log(_liHtml)

		jQuery('#_action_history ul').html( _liHtml );
}

// date diff formater
function timeBetweenDates(toDate,res) {
	  var dateEntered = toDate;
	  var now = new Date();
	  var difference = dateEntered.getTime() - now.getTime();

	  if (difference <= 0) {
	  	//console.log('ended ??')
	    // console.log('done');
	    if (res.tokenOwner != "0x0000000000000000000000000000000000000000" && currentAccount && res.firstBidTime != "0"){
	    	jQuery('#_auction_fr_end_auction').css('display','block');
            jQuery('#_auction_amount').css('display','none');
            jQuery('#_auction_fr_place_bid').css('display','none');
	    }
	    
	    
	    //jQuery('#_bidder_amount').css('display','none');
	    
	    return [0,0,0,0]

	  } else {
		if (res.approved && currentAccount){
			jQuery("#_auction_amount").css("display","block");
			jQuery("#_auction_amount_desc").css("display","block");
		}
	  	

	    var seconds = Math.floor(difference / 1000);
	    var minutes = Math.floor(seconds / 60);
	    var hours = Math.floor(minutes / 60);
	    var days = Math.floor(hours / 24);

	    hours %= 24;
	    minutes %= 60;
	    seconds %= 60;

			// console.log(days);
	  //   	console.log(hours);
			// console.log(minutes);
			// console.log(seconds);
			//jQuery('#_auction_fr_place_bid').css('display','block');
			return [days,hours,minutes,seconds]

	  }
	}


// update timer

async function timerUpdate(){
	let res = await GetAuctionDetails2(nftData.auction);
	// console.log(res);
	if (res.tokenOwner != "0x0000000000000000000000000000000000000000"){
		jQuery('#__token__owner').html( res.tokenOwner );
		jQuery('#__token__owner').attr('href', 'https://etherscan.io/address/' + res.tokenOwner)
		if (res.bidder != "0x0000000000000000000000000000000000000000"){
			jQuery('#_bidder').text(res.bidder);
		jQuery('#_bidder_amount').text(Web3.utils.fromWei(res.amount,'ether')+ ' '+nftData.network_option.symbol);
		jQuery("._bidder_amount").text(Web3.utils.fromWei(res.amount,'ether')+ ' '+nftData.network_option.symbol);
		jQuery('#_bidder').css("display","block");
			jQuery('#_bidder_amount').css("display","block");
			jQuery("._bidder_amount").css("display","block");
			
		}else{
			jQuery('#_bidder').css("display","none");
			jQuery('#_bidder_amount').css("display","none");
			jQuery("._bidder_amount").css("display","none");
		}
		
	}else{
		jQuery('#__token__owner').css('display','none');
		jQuery('#_bidder').css('display','none');
		jQuery('#_bidder_amount').css("display","none");
		jQuery("._bidder_amount").css("display","none");
	}
	let time;
	if (res.firstBidTime != "0"){
		let d1 = new Date((Number(res.duration) + Number(res.firstBidTime)) * 1000);
		time = timeBetweenDates(d1,res);
	}else if(res.firstBidTime == "0" && res.tokenOwner != "0x0000000000000000000000000000000000000000"){
		let now = new Date();
		//console.log(res);
		let d1 = new Date(now.getTime() + (Number(res.duration) * 1000));
		//console.log(d1);
		time = timeBetweenDates(d1,res);
		time[3] = 0
	}else{
		time = [0,0,0,0]
	}
	//console.log(time)
	
	jQuery('#_auction_timing').css({ 'display': 'block' });
	jQuery('#_day').text(time[0]+"d");
	jQuery('#_hours').text(time[1]+"h");
	jQuery('#_mins').text(time[2]+"m");
	jQuery('#_secs').text(time[3]+"s");
	



}

async function Approve(){
	let c = loadContract2(HouseAbi,nftData.network_option.contract_address);
	let res = await c.methods.setAuctionApproval(nftData.auction,true).send({from:currentAccount});
	if (res){
		console.log(res);
	}
	
}

async function EndAuction(){
	let c = loadContract2(HouseAbi,nftData.network_option.contract_address);
	let res = await c.methods.endAuction(nftData.auction).send({from:currentAccount});
	console.log(res);
}


async function refreshAccountData() {
	await fetchAccountData(provider);
  }
  
  
  async function fetchAccountData() {
	// Get a Web3 instance for the wallet
	const web3 = new Web3(provider);
	window.w3 = web3;
  
	console.log("Web3 instance is", web3);
  
	// Get list of accounts of the connected wallet
	const accounts = await web3.eth.getAccounts();
  
	// MetaMask does not give you all accounts, only the selected account
	console.log("Got accounts", accounts);
	currentAccount = accounts[0];
	await checkOpen();
	
	jQuery('#_auction_fr_connect').css({'display' : 'none'});
  }
  
  async function onConnect() {
	console.log("Opening a dialog", web3Modal);
	try {
	  provider = await web3Modal.connect();
	} catch (e) {
	  console.log("Could not get a wallet connection", e);
	  return;
	}
  
	// Subscribe to accounts change
	provider.on("accountsChanged", (accounts) => {
	  fetchAccountData();
	});
  
	// Subscribe to chainId change
	provider.on("chainChanged", (chainId) => {
	  fetchAccountData();
	});
  
	// Subscribe to networkId change
	provider.on("networkChanged", (networkId) => {
	  fetchAccountData();
	});
  
	await refreshAccountData();
  }

  async function onDisconnect() {

	console.log("Killing the wallet connection", provider);
  
	// TODO: Which providers have close method?
	if(provider.close) {
	  await provider.close();
  
	  // If the cached provider is not cleared,
	  // WalletConnect will default to the existing session
	  // and does not allow to re-scan the QR code with a new wallet.
	  // Depending on your use case you may want or want not his behavir.
	  await web3Modal.clearCachedProvider();
	  provider = null;
	}
  
	selectedAccount = null;
  
	// Set the UI back to the initial state
	document.querySelector("#prepare").style.display = "block";
	document.querySelector("#connected").style.display = "none";
  }



  async function setNetwork(){
	try{
	  const Web3Modal = window.Web3Modal.default;
	const WalletConnectProvider = window.WalletConnectProvider.default;
	const Fortmatic = window.Fortmatic;
	const evmChains = window.evmChains;
	let v = String(nftData.network_option.chain_id);
	
	console.log(v);
  
	  /* if (provider.close) {
		await provider.close();
  
		// If the cached provider is not cleared,
		// WalletConnect will default to the existing session
		// and does not allow to re-scan the QR code with a new wallet.
		// Depending on your use case you may want or want not his behavir.
		await web3Modal.clearCachedProvider();
		provider = null;
	  } */
	  let opt = {}
	  let rpc_option = {};
	  if (Object.keys(ChainDataList).includes(v)){
		v = Number(v);
		if (!([1,3,4,5].includes(Number(v)))){
		  let rpc_url = nftData.network_option.rpc_url;
		  rpc_option = {
			rpc : {
			  v : rpc_url
			},
			network : ChainDataList[v]
		  }
		}else{
		  let rpc_url = nftData.network_option.rpc_url;
		  rpc_option = {
			
			infuraId:rpc_url,
			netwok:ChainDataList[v].network,
		  }
		}
		opt = {
		  walletconnect:{
			package: WalletConnectProvider,
			options:  rpc_option,
	  
		}
	  }
	}
  
	  const providerOptions = opt;
  
	  window.web3Modal = new Web3Modal({
		cacheProvider: false, // optional
		providerOptions, // required
		disableInjectedProvider: false, // optional. For MetaMask / Brave / Opera.
	  });
	  await web3Modal.clearCachedProvider();
	}catch (e){
	  console.log(e);
	}
  }

  async function checkOpen(){
	if (currentAccount){
	  let c_id = await w3.eth.getChainId();
	 
	  if (!(Number(c_id) == Number(nftData.network_option.chain_id))){
		showMsg();
	  }else{
		  hideMsg();
	  }
	}
  }

function showMsg(){
	document.getElementById("_network_error").style.display = "block";
}

function hideMsg(){
	document.getElementById("_network_error").style.display = "none";
}


window.addEventListener("load", async function () {
	hideMsg();
	document.getElementById("_network_error").innerHTML = "Please connect to "+nftData.network_option.network_name;
	setNetwork();
	ShopProducts();
	let modal = document.querySelector('.web3modal-modal-lightbox');
	modal.style.zIndex = "100";
	jQuery("._bidder_amount").text("0.00 "+nftData.network_option.symbol);
	if( document.getElementById("_auction_fr_place_bid") ){
        // create bid button
        const btnCreateBid = document.getElementById("_auction_fr_place_bid");
        const btnConnect = document.getElementById("_auction_fr_connect");
        const btnEnd = document.getElementById("_auction_fr_end_auction");
        const approveBtn = document.getElementById("_auction_fr_approve");
        btnCreateBid.addEventListener("click", function() { createBid(nftData.auction, document.getElementById("_auction_amount").value) } );
        btnConnect.addEventListener("click",onConnect);
        btnEnd.addEventListener("click",EndAuction);
        approveBtn.addEventListener("click",Approve);
		jQuery("#_auction_owned_by").css("display","none");
		jQuery("#_auction_amount").css("display","none");
		jQuery("#_auction_amount_desc").css("display","none");
		jQuery("#_auction_amount").attr("placeholder", "Amount (5% more than current bid)");
		setInterval(handleUI,1000);
    }else{
      // console.log( "nothing found" );
    }

});

//0x0000000000000000000000000000000000000000