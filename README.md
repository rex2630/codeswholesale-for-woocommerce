codeswholesale-woocommerce
==========================

CodesWholesale integration plugin for WooCommerce

TO DO
-----
* Automatically complete order when payment is received
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
* Cron job to update stock information
* Cron job to check that all completed orders are full filled with keys


How to use
----------
1. Download and install into your WordPress - WooCommerce plugin is required.
2. CodesWholesale tab will apear in admin menu. Under this tab you can configure which endpoint you would like to use. For start we recommend Sandbox to test behaviour of plugin and to check if keys are really send to your customer.
3. While editing a product you should see a new field in "General tab" to connect your product with CodesWholesale product. For a start you can match one to "Test with text codes only".
4. Log out from WordPress admin panel, add this product to cart go through checkout process to create an order.
