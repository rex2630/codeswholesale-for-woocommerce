<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p>
    Sorry, your balance is too low and soon you will not be able to make an order. Now it's necessary to raise your balance at <a href="https://app.codeswholesale.com">CodesWholesale.com</a>. <br /><br />
    Add money to your account and provide fluent operations in your web store. <br /><br />

    Your current balance: <?php echo CodesWholesaleConst::format_money($account->getCurrentBalance()); ?>
</p>

<?php do_action('woocommerce_email_footer'); ?>