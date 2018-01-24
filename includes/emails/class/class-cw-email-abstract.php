<?php
if (!class_exists("WC_Email")) {
    include(WC()->plugin_path() . "/includes/abstracts/abstract-wc-email.php");
}

abstract class CW_Email_Abstract extends WC_Email
{
    public $cw_content;
    public $wp_email;
    
    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();       
    }
     
    public function get_cw_heading($wp_post = null) 
    {
        if(null !== $wp_post) {
            return get_post_meta( $wp_post->ID, 'codeswholesale_email_heading', true );  
        } else {
           return get_post_meta( $this->wp_email->ID, 'codeswholesale_email_heading', true );  
        }
       
    }
    
    public function get_cw_subject($wp_post = null) 
    {
        if(null !== $wp_post) {
            return get_post_meta( $wp_post->ID, 'codeswholesale_email_subject', true );  
        } else {
            return get_post_meta( $this->wp_email->ID, 'codeswholesale_email_subject', true );  
        }
        

    }
    
    public function get_cw_id($wp_post = null) 
    {
        if(null !== $wp_post) {
            return get_post_meta( $wp_post->ID, '_codeswholesale_email_id', true );
        } else {
            return get_post_meta( $this->wp_email->ID, '_codeswholesale_email_id', true );
        }
       
    }
    
    public function get_cw_title($wp_post = null) 
    {
        if(null !== $wp_post) {
            return  $wp_post->post_title; 
        } else {
            return  $this->wp_email->post_title;  
        }
       
    }
    
    public function get_cw_content($wp_post = null) 
    {
        if(null !== $wp_post) {
            return  $wp_post->post_content;
        } else {
            return  $this->wp_email->post_content;
        }
      
    }
    public function get_cw_shortcode($content, $tag) 
    {
        if ( false === strpos( $content, '[' ) ) {
            return null;
        }

        $regex = "\[(\[?)(".$tag.")(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)";
   
        preg_match_all( '/' . $regex . '/', $content, $matches);
		
        if ( empty( $matches ) ) {
            return null;
        } else {
            return $matches[0][0];
        }
                
    }


}

