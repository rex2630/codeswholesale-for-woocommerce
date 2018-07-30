<div class="cw-header cw-row">
    <div class="cw-logo">
        <div class="cw-logo-ct"></div>
    </div>
    <div class="cw-connection">
        <ul id="cw-connection-info">
            <?php if ($this->acountError) : ?>

                 <li class="updated">
                     <h3>
                         <small><?php _e('Connection status', 'woocommerce')?></small><br>
                         <span class="cw-failed"><?php _e('Connection failed.', 'woocommerce')?></span>
                     </h3>
                 </li>

                 <li>
                    <h3>
                         <small><?php _e('Error', 'woocommerce')?>:</small><br>
                         <span><?php echo $this->acountError->getMessage(); ?></span>
                     </h3>
                 </li>

             <?php endif; ?>

             <?php if ($this->account) : ?>

                 <li class="updated">
                    <h3>
                        <small><?php _e('Connection status', 'woocommerce')?></small><br>
                        <span class="cw-success"><?php _e('Successful', 'woocommerce')?></span>
                    </h3>
                     
                 </li>
                 <li>
                    <h3>
                        <small><?php _e('Account', 'woocommerce')?>:</small><br>
                        <span> <?php echo $this->account->getFullName(); ?></span>
                    </h3>                    
                 </li>

                 <li>
                    <h3>
                        <small><?php _e('Account balance', 'woocommerce')?>:</small><br>
                        <span> <?php echo "€" . number_format($this->account->getTotalToUse(), 2, '.', ''); ?></span>
                    </h3> 
                 </li>
                <li>
                    <h3>
                        <small><?php _e('Email address', 'woocommerce')?>:</small><br>
                        <span> <?php echo $this->account->getEmail(); ?></span>
                    </h3>  
                 </li>
             <?php endif; ?>
        </ul>
    </div>
</div>



