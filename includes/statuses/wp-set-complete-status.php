<?php

class WP_Set_Complete_Status
{
    public function setStatus($orderDetails, $total_number_of_keys){

        $orderDetails->add_order_note(sprintf("Game keys sent (total: %s).", $total_number_of_keys));
    }
}
