codeswholesale-woocommerce
==========================

CodesWholesale integration plugin for WooCommerce

TO DO
-----
* Automatically complete order when payment is received
* Cron job to update stock information
* Cron job to check that all orders are full filled with keys
* Somehow support preorders


Nice to have
------------
* Post back query from CodesWholesale to shop about stock instead cron (benefit: more accurate stock details)
* Post back query from CodesWholesale to shop to let know that preorder is ready to download

What it does
------------
* Adds a field to product to match with CodesWholesale's product in "General tab" 
* Buys and sends keys (text and images) when order is marked as complete
* Disable with plugin activation, WooCommerce's email notification about completed order
