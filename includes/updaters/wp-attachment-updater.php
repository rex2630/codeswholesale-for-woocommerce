<?php
if( !class_exists( 'WP_Http' ) )
	include_once( ABSPATH . WPINC. '/class-http.php' );

class WP_Attachment_Updater
{
    public function setAttachment(int $postid, string $url, string $name) {
        $http = new WP_Http();
        $photo = $http->request($url); 

        if( is_wp_error( $photo )) {
            throw new \Exception($photo->get_error_message());
        }
                
        $attachment = wp_upload_bits( wc_clean($name), null, $photo['body'], date("Y-m", strtotime( $photo['headers']['last-modified'] ) ) );

        $filetype = wp_check_filetype( basename( $attachment['file'] ), null );

        $postinfo = array(
            'post_mime_type'=> $filetype['type'],
            'post_title'	=> wc_clean($name),
            'post_content'	=> '',
            'post_status'	=> 'inherit',
        );

        $filename = $attachment['file'];

        $attach_id = wp_insert_attachment( $postinfo, $filename, $postid );

        if( !function_exists( 'wp_generate_attachment_data' ) )
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');

        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

        wp_update_attachment_metadata( $attach_id,  $attach_data );

        return $attach_id;
      }  
}

