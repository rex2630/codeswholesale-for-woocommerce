<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * Class CW_Admin
 */
class CW_Emails 
{
    /**
     * Constructor
     */
    public function __construct() {
        $this->includes();
    }

    /**
     * Include any classes we need within emails.
     */
    public function includes() {
        include_once('default/default-interface.php');
        include_once('default/default-model.php');
        include_once('default/customer-completed-order.php');
        include_once('default/customer-completed-pre-order.php');
        include_once('default/notify-import-finished.php');
        include_once('default/notify-low-balance.php');
        include_once('default/notify-preorder.php');
        include_once('default/order-error.php');
                
        include_once('prepare/class-wp-emaila-custom-post.php');
        include_once('prepare/class-wp-radio-taxonomy.php');
        include_once('prepare/class-wp-email-custom-post-generator.php');
                
        include_once('wp-send-code-mail.php');
        include_once('wp-admin-error-mail.php');
        include_once('wp-admin-general-error.php');
        include_once('wp-admin-import-finished.php');
    }
    
}

final class CW_EmailsConst
{
    const CW_EMAIL_POST_TYPE        = "codeswholesale_email";
    const CW_EMAIL_TAXONOMY_TYPE    = "codeswholesale_email_type";
    
    const CW_EMAIL_TYPE_PRE_ORDER_COMPLETED     = "CW_Email_Customer_Completed_Pre_Order";
    const CW_EMAIL_TYPE_ORDER_COMPLETED         = "CW_Email_Customer_Completed_Order";
    const CW_EMAIL_TYPE_NOTIFY_IMPORT_FINISHED  = "CW_Email_Notify_Import_Finished"; 
    const CW_EMAIL_TYPE_NOTIFY_LOW_BALANCE      = "CW_Email_Notify_Low_Balance";  
    const CW_EMAIL_TYPE_NOTIFY_PREORDER         = "CW_Email_Notify_Preorder";
    const CW_EMAIL_TYPE_ORDER_ERROR             = "CW_Email_Order_Error";    
    
    const CW_EMAIL_SHORTCODE_HEADING        = "codeswholesale-email-heading";
    const CW_EMAIL_SHORTCODE_SUBJECT        = "codeswholesale-email-subject";
    const CW_EMAIL_SHORTCODE_LOOP_KEYS      = "codeswholesale-email-loop-keys";
    
    public static function getShortcodeKeys() {
        return [
            self::CW_EMAIL_SHORTCODE_HEADING,
            self::CW_EMAIL_SHORTCODE_SUBJECT,
            self::CW_EMAIL_SHORTCODE_LOOP_KEYS
        ];
    }
    
    public static function getDefaultEmailsKeys() {
        return [
            self::CW_EMAIL_TYPE_ORDER_COMPLETED,
            self::CW_EMAIL_TYPE_PRE_ORDER_COMPLETED,
            self::CW_EMAIL_TYPE_NOTIFY_IMPORT_FINISHED,
            self::CW_EMAIL_TYPE_NOTIFY_LOW_BALANCE,
            self::CW_EMAIL_TYPE_NOTIFY_PREORDER,
            self::CW_EMAIL_TYPE_ORDER_ERROR 
        ];
    }
    
    public static function getCustomEmail($key) {
        $posts_array = get_posts(
                array(
                    'posts_per_page' => -1,
                    'post_type'      => CW_EmailsConst::CW_EMAIL_POST_TYPE,
                    'post_status'    => 'publish',
                    'tax_query'      => array(
                        array(
                            'taxonomy'  => CW_EmailsConst::CW_EMAIL_TAXONOMY_TYPE,
                            'field'     => 'slug',
                            'terms'     => strtolower($key),
                        )
                    )
                )
            ); 
        
        if (count($posts_array)>0) {
            return $posts_array[0];
        } else {
            return null;
        }
    }
}


return new CW_Emails();