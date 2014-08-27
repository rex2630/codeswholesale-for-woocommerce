<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p>
    Your order has been received and is now being processed. You have bought <?echo $count ?> pre-ordered key(s) at <a href="https://app.codeswholesale.com">CodesWholesale.com</a> for <?php echo $item['name']; ?>.
</p>

<?php do_action('woocommerce_email_footer'); ?>