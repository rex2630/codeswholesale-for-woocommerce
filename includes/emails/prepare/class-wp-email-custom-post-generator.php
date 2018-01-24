<?php
if ( !class_exists( 'WP_CustomPostEmailsGenerator' ) ) {
   /**
    * Removes and replaces the built-in taxonomy metabox with our radio-select metabox.
    * @link  http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
    */
   class WP_CustomPostEmailsGenerator {
       
        public function init() {
           $emailKeys = CW_EmailsConst::getDefaultEmailsKeys();
           
           foreach($emailKeys as $key) {
               if( null == CW_EmailsConst::getCustomEmail($key)) {
                    $class = $key."_Default";
                    $this->createPost( new $class(), $key);
               } 
           }
        }
               
        private function createPost(CW_DefaultEmailDataInterface $defaultEmailDataModel, string $key) {

            $args = array(
                'post_author' => get_current_user_id(),
                'post_content' => $defaultEmailDataModel->getContent(),
                'post_title' => $defaultEmailDataModel->getTitle(),
                'post_status' => 'Publish',
                'post_type' => CW_EmailsConst::CW_EMAIL_POST_TYPE,
                'comment_status' => 'closed',
                'ping_status'   => 'closed',
            );
               
            $post_id = wp_insert_post( $args );
            add_post_meta($post_id, 'codeswholesale_email_heading', $defaultEmailDataModel->getHeading(), true);
            add_post_meta($post_id, 'codeswholesale_email_subject', $defaultEmailDataModel->getSubject(), true);
            add_post_meta($post_id, '_codeswholesale_email_id', $defaultEmailDataModel->getId(), true);
            
            wp_set_object_terms( $post_id, strtolower($key), CW_EmailsConst::CW_EMAIL_TAXONOMY_TYPE);
            
            
        }
   }
}

