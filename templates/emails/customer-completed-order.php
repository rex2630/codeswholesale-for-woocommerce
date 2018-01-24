<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<?php do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text); ?>

<?php echo $content ?>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_footer'); ?>