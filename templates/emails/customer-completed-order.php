<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

    <p><?php printf(__("Hi there. Your recent order on %s has been completed. Your keys are shown below for your reference:", 'woocommerce'), get_option('blogname')); ?></p>

<?php do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text); ?>

    <h3><?php echo __('Order:', 'woocommerce') . ' ' . $order->get_order_number(); ?></h3>

<?php foreach ($keys as $key) : ?>

    <b><?php echo $key['item']['name']; ?> </b> <br/>

    <?php foreach ($key['codes'] as $code) : ?>

        <?php
            if($code->isText())
            {
                echo $code->getCode();
            }

            else if($code->isPreOrder()) {

                echo 'This key is Pre-Order';

            }

            else if ($code->isImage())
            {
                echo 'Check in attachment file: '. $code->getFileName();
            }
        ?>

        <br />

    <?php endforeach; ?>

    <br />

<?php endforeach; ?>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_footer'); ?>