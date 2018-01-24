<?php

require_once( dirname(__FILE__) . '/../../../../../wp-load.php' );

global $wpdb;

$wpdb->get_results("SHOW TABLES");