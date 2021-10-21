function handleAccountsChanged(accounts) {
  if (accounts.length === 0) {
    console.log("Please connect to MetaMask.");
    jQuery("#enableMetamask").html("Connect with Metamask");
  } else if (accounts[0] !== currentAccount) {
    currentAccount = accounts[0];
    jQuery("#connect-btn").css("display", "none");

    jQuery("#nftauction_type_product_options").css("display", "block");

    if (jQuery("#approve").length > 0) {
      jQuery("#approve").css("display", "inline-block");
      let btn = jQuery("#auction-create")[0];
      let btn2 = jQuery("#approve")[0];
      btn2.addEventListener("click", Approve);
      btn.addEventListener("click", createAuctionHandler);
    }

    if (jQuery("#auction-cancel").length > 0) {
      let btn = jQuery("#auction-cancel")[0];
      btn.addEventListener("click", cancelZoraAuction);
    }

    w3 = new Web3(window.ethereum);
    if (currentAccount != null) {
      // Set the button label
      jQuery("#enableMetamask").html(currentAccount);
      document.getElementById("_owner").value = currentAccount;
    }
  }
}

// using this

async function cancelZoraAuction() {
  // the id is the auction Id how do yoou access it ?
  let id = document.getElementById("_auction_id").value;
  let c = loadContract(CustomHouseAbi, getContractAdd());
  let resp = await c.methods.cancelAuction(id).send({ from: currentAccount });
  if (resp) {
    console.log("auction canceled");
    console.log(resp);
    document.getElementById("_auction_status").value = "Cancelled";
  } else {
    console.log("failed");
  }
}

function convertDate(d) {
  let date2 = new Date();
  let date = new Date(d);
  return (date.getTime() - date2.getTime()) / 1000;
}

function calculateDuration(d, h, m, s) {
  return Number(d) * 3600 * 24 + Number(h) * 3600 + Number(m) * 60 + Number(s);
}

function loadContract(abi, add) {
  let c = new w3.eth.Contract(abi, add);
  return c;
}

function createAuctionHandler() {
  if (create_validations()) {
    alert("starting");
    let tokId = jQuery("#_token_id")[0].value;
    let tokC = jQuery("#_token_contract")[0].value;
    //let duration = jQuery('#_auction_duration')[0].value;
    //let day = jQuery('#_auction_duration_day')[0].value;
    //let hours = jQuery('#_auction_duration_hours')[0].value;
    //let minutes = jQuery('#_auction_duration_minutes')[0].value;
    //let seconds = jQuery('#_auction_duration_seconds')[0].value;
    let reserve = jQuery("#_reserve_price")[0].value;
    let curator = jQuery("#_curator")[0].value;
    let fee = jQuery("#_curator_fee_percent")[0].value;
    let curr = jQuery("#_auction_currency")[0].value;
    let v = document.getElementById("_token_network").value;
    let network_options =  Object.values(nftData.network_options);
    let n_o = network_options.filter((e) => Number(e.chain_id) == Number(v))[0]
    console.log(n_o);
    curator = curator === "" ? defaultCurrator : curator;
    curr = curr === "" ? EthAdd : curr;

    console.log(curator);
    console.log(curr);
    let duration = parseInt(
      document.getElementById("_auction_duration_picker").value
    );
    console.log(String(duration));

    let c = loadContract(CustomHouseAbi, n_o['contract_address']);

    c.methods
      .createAuction(
        Number(tokId),
        tokC,
        String(duration),
        Web3.utils.toWei(String(reserve), "ether"),
        curator,
        Number(fee),
        curr
      )
      .send({ from: currentAccount })
      .then((res) => {
        console.log("result here");
        console.log(res);
        alert(
          "Auction Created with Id : " +
            res.events.AuctionCreated.returnValues.auctionId
        );
        jQuery("#auction-create").hide();
        // Save value for auction ID
        document.getElementById("_auction_id").value =
          res.events.AuctionCreated.returnValues.auctionId;
        document.getElementById("_block_number").value = res.blockNumber;
      });

    /* c.AuctionCreated(function(error,result){
          alert('even triggered');
          console.log(result);
      }) */

    /* c.getPastEvents('AuctionCreated', {}).then(res => console.log(res)); */
  }
}

function getContractAdd(){
  let v = document.getElementById("_token_network").value;
  let network_options =  Object.values(nftData.network_options);
  let n_o = network_options.filter((e) => Number(e.chain_id) == Number(v))[0];
  return n_o['contract_address'];
}

async function Approve() {
  // Validation for approve button
  if (approve_validations()) {
    let tokId = jQuery("#_token_id")[0].value;
    let tokC = jQuery("#_token_contract")[0].value;
    let c = loadContract(NftAbi, tokC);
    let CurrentApproved = await c.methods.getApproved(Number(tokId)).call();
    let v = document.getElementById("_token_network").value;
    let network_options =  Object.values(nftData.network_options);
    let n_o = network_options.filter((e) => Number(e.chain_id) == Number(v))[0]

    if (CurrentApproved == n_o['contract_address']) {
      alert("already approved");
      jQuery("#approve").css("display", "none");
      jQuery("#auction-create").css("display", "inline-block");
    } else {
      c.methods
        .approve(n_o['contract_address'], tokId)
        .send({ from: currentAccount })
        .then((res) => {
          alert("nft approved");
          jQuery("#approve").css("display", "none");
          jQuery("#auction-create").css("display", "inline-block"); // here is success  login
        })
        .catch((res) => alert("failed"));
    }
  }
}

function approve_validations() {
  if (jQuery("#_token_id").val() == "" || jQuery("#_token_id").val() == null) {
    jQuery("#_token_id").css({ border: "solid 1px #ff0000" });
    jQuery("html, body").animate(
      {
        scrollTop: jQuery("#nft_auction_options").offset().top,
      },
      2000
    );
    return false;
  }
  if (
    jQuery("#_token_contract").val() == "" ||
    jQuery("#_token_contract").val() == null
  ) {
    jQuery("#_token_contract").css({ border: "solid 1px #ff0000" });
    jQuery("html, body").animate(
      {
        scrollTop: jQuery("#nft_auction_options").offset().top,
      },
      2000
    );
    return false;
  }

  return true;
}

function create_validations() {
  /* if( jQuery('#_auction_duration_day').val() == '' || jQuery('#_auction_duration_day').val() == null ){
      jQuery('#_auction_duration_day').css({ 'border': 'solid 1px #ff0000' });
      jQuery('html, body').animate({
        scrollTop: jQuery("#nft_auction_options").offset().top
      }, 2000);
      return false;
    }
    if( jQuery('#_auction_duration_hours').val() == '' || jQuery('#_auction_duration_hours').val() == null ){
      jQuery('#_auction_duration_hours').css({ 'border': 'solid 1px #ff0000' });
      jQuery('html, body').animate({
        scrollTop: jQuery("#nft_auction_options").offset().top
      }, 2000);
      return false;
    }
    if( jQuery('#_auction_duration_minutes').val() == '' || jQuery('#_auction_duration_minutes').val() == null ){
      jQuery('#_auction_duration_minutes').css({ 'border': 'solid 1px #ff0000' });
      jQuery('html, body').animate({
        scrollTop: jQuery("#nft_auction_options").offset().top
      }, 2000);
      return false;
    } */
  /* if (
    jQuery("#_auction_duration_seconds").val() == "" ||
    jQuery("#_auction_duration_seconds").val() == null
  ) {
    jQuery("#_auction_duration_seconds").css({ border: "solid 1px #ff0000" });
    jQuery("html, body").animate(
      {
        scrollTop: jQuery("#nft_auction_options").offset().top,
      },
      2000
    );
    return false;
  } */
  if (
    jQuery("#_reserve_price").val() == "" ||
    jQuery("#_reserve_price").val() == null
  ) {
    jQuery("#_reserve_price").css({ border: "solid 1px #ff0000" });
    jQuery("html, body").animate(
      {
        scrollTop: jQuery("#nft_auction_options").offset().top,
      },
      2000
    );
    return false;
  }
  if (
    jQuery("#_curator_fee_percent").val() == "" ||
    jQuery("#_curator_fee_percent").val() == null
  ) {
    jQuery("#_curator_fee_percent").css({ border: "solid 1px #ff0000" });
    jQuery("html, body").animate(
      {
        scrollTop: jQuery("#nft_auction_options").offset().top,
      },
      2000
    );
    return false;
  }

  return true;
}

function connectHandler() {
  connect();
  setInterval(connect, 1000);
}

function connect() {
  ethereum
    .request({ method: "eth_requestAccounts" })
    .then(handleAccountsChanged)
    .catch((err) => {
      if (err.code === 4001) {
        console.log("Please connect to MetaMask.");
      } else {
        console.error(err);
      }
    });
}

function test(event) {
  event.preventDefault();
  console.log(this.getAttribute("data-token"));
}

async function GetAuctionDetails(auctionId) {
  let c = loadContract(CustomHouseAbi, getContractAdd());
  let resp = await c.methods.auctions(auctionId).call();
  return resp;
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
  checkOpen();
  if (currentAccount != null) {
    document.getElementById("_owner").value = currentAccount;
  }
  jQuery("#connect-btn").css("display", "none");

  jQuery("#nftauction_type_product_options").css("display", "block");

  if (jQuery("#approve").length > 0) {
    jQuery("#approve").css("display", "inline-block");
    let btn = jQuery("#auction-create")[0];
    let btn2 = jQuery("#approve")[0];
    btn2.addEventListener("click", Approve);
    btn.addEventListener("click", createAuctionHandler);
  }

  if (jQuery("#auction-cancel").length > 0) {
    let btn = jQuery("#auction-cancel")[0];
    btn.addEventListener("click", cancelZoraAuction);
  }
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
  await checkOpen();
}

async function setNetwork(){
  try{
    const Web3Modal = window.Web3Modal.default;
  const WalletConnectProvider = window.WalletConnectProvider.default;
  const Fortmatic = window.Fortmatic;
  const evmChains = window.evmChains;
  let v = document.getElementById("_token_network").value;
  
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
    
    let network_options =  Object.values(nftData.network_options);
    let cont = network_options.filter((e) => Number(e.chain_id) == Number(v))[0]
    let opt = {}
    let rpc_option = {};
    if (Object.keys(ChainDataList).includes(v)){
      v = Number(v);
      if (!([1,3,4,5].includes(Number(v)))){
        
        let rpc_url = cont.rpc_url;
        rpc_option = {
          rpc : {
            v : rpc_url
          },
          network : ChainDataList[v]
        }
      }else{
        
        let rpc_url = cont.rpc_url;
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
    document.querySelector('._popup_network_name').innerHTML = "Please Connect to " + cont.network_name;
    
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
    let v = document.getElementById("_token_network").value;
    if (!(Number(c_id) == Number(v))){
      openOverlay();
    }
  }
}

function openOverlay(){
  let overlay = document.getElementById('_auction_popup');
  overlay.style.visibility = "visible";
  overlay.style.opacity = "1";
  
}

function closeOverlay(){
  let overlay = document.getElementById('_auction_popup');
  overlay.style.visibility = "hidden";
  overlay.style.opacity = "0";
}

window.addEventListener("load", async function () {
  try {
    let overlay = document.getElementById('_auction_popup');
    overlay.addEventListener('click',closeOverlay);
    currentAccount = null;
    await setNetwork();
    if (this.document.getElementById("_token_network")){
      let elem_ = this.document.getElementById("_token_network");
      elem_.addEventListener("change",async () => {
        await setNetwork();
        await checkOpen();
      }
        );
    }
    if (document.getElementById("connect-btn")) {
      const btn = document.getElementById("connect-btn");
      
      btn.addEventListener("click", onConnect);
    }

    if (defaultCurrator) {
      if (document.getElementById("_curator")) {
        // in case already have a value - don't change
        if (document.getElementById("_curator").value !== "") {
          // nothing to do here
        } else {
          document.getElementById("_curator").value = defaultCurrator;
        }
      }
    }

    let _inputElements = [
      "#_token_id",
      "#_token_contract",
      "#_auction_duration",
      "#_reserve_price",
      "#_curator_fee_percent",
      "#_auction_id",
      "#_block_number",
    ];

    let _return = false;

    _inputElements.forEach(function (item, index) {
      jQuery(item).on("change", function () {
        jQuery(this).css({ "border-color": "#8c8f94" });
      });
    });
  } catch (e) {
    console.log(e);
  }
});
