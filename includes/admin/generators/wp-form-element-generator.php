<?php
/**
 * CW_Form_Element_Generator
 */
class WP_Form_Element_Generator {

    public function codeswholesale_wp_radio($field) {
        woocommerce_wp_radio($field);
    }
    
    public function codeswholesale_wp_checkbox($field) {
        woocommerce_wp_checkbox($field);
    }
   
    public function codeswholesale_wp_checkboxes($field) {
        
        echo '<fieldset class="' .$field['wrapper_class']. '"><ul>';
            foreach($field['options'] as $key => $value) {
                 echo '<li>
                        <label>
                            <input
                                ' . $field['checked'] . '
                                 name="' . $field['name'] . '"
                                 value="' . esc_attr( $key ) . '"
                                 type="checkbox" />' . 
                            $value .
                         '</label>
                     </li>';
            }
        echo '</ul></fieldset>';
    }
    
    public function codeswholesale_wp_sleect($field) {
        woocommerce_wp_sleect($field);
    }
}



