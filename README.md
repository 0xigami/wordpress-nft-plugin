# NFT Auction Product Type for WooCommerce – BID HAUS
BID HAUS is a [WordPress NFT Plugin](https://art.haus/wordpress-nft-plugin/) for auctions with WooCommerce, built by [ART HAUS](https://art.haus) and powered by the [ZORA Auction House contract](https://docs.zora.co/docs/smart-contracts/zora-contracts#auction-house) on Ethereum. It's free and enables you to auction any ERC-721 with 0% marketplace fees and automated curator earnings.

![](https://raw.githubusercontent.com/artdothaus/brand-assets/main/wordpress-nft-plugin-for-woocommerce.jpg)

## Turn Your WooCommerce Shop into an NFT Marketplace

Visitors to your WordPress website are presented with the WooCommerce experience they are already familiar with. Products that you've set as [NFT Auctions](https://art.haus/shop/) will have the usual checkout experience replaced with a simple Web3 flow. Collectors simply connect their wallet via either MetaMask or WalletConnect integration and are then able to place on-chain bids directly from the product page.

![](https://raw.githubusercontent.com/artdothaus/brand-assets/main/wordpress-nft-plugin-demo.png)

## Enable NFT Auction Product Type in WooCommerce Admin

Once the plugin is installed and activated you will find a new Product Type available in the Product data dropdown selector. By choosing "NFT Auction" and visiting the corresponding tab, you will find all the necessary fields to approve your NFT and create an auction using [ZORA's Auction House contract](https://docs.zora.co/docs/smart-contracts/zora-contracts#auction-house) on Ethereum. MetaMask or WalletConnect integration are both available for connection.

![](https://raw.githubusercontent.com/artdothaus/brand-assets/main/wordpress-nft-plugin.jpg)

## Getting Started

### Installing the Plugin

1. [Download ZIP](https://github.com/artdothaus/wordpress-nft-plugin/archive/refs/heads/main.zip) of this repo
2. Login to your WordPress admin
3. Visit Plugins > Add New > Upload
4. "Choose file" and select ZIP downloaded
5. Click "Install Now" then "Activate"

### Creating an NFT Auction

1. Create a new product, scroll to "Product data"
2. Select "NFT Auction" and then corresponding tab
3. Choose which blockchain Network you are using
4. Enter your Token ID and Token Contract
5. Set auction Duration and Reserve Price
6. Nominate a Curator and Fee % (optional)
7. Enter Auction Currency (token contract address) or leave blank for ETH
8. Connect to wallet containing the NFT entered
9. Approve spending of NFT and confirm transaction
10. Create Auction and confirm transaction

### Further Reading

Your NFT has now been transferred to ZORA's Auction House contract which provides escrow for the duration of your auction, transfers the NFT upon successful sale, and distributes funds to Owner and Curator as specified. 

Now you can Publish your product or Preview it to inspect how things look on the front end. If you've made a mistake and need the NFT returned, you can Cancel Auction from the product admin page.

Auction will run for Days/Hours/Minutes you've set. Countdown commences upon first successful bid greater than the Reserve Price.

**PLEASE NOTE:** You will also need to Connect from the front end and *Approve* the auction (and confirm transaction) as it's a requirement of the ZORA Auction House contract. This is intended to be done by the nominated Curator, or alternatively by the Artist or Collector you are selling on behalf of. If you are the only involved party, you can Approve from a different wallet address.

For more information, please visit the [ZORA documentation](https://docs.zora.co).
