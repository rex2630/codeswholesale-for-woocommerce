<?php

class WP_Admin_Notify_Import_Finished
{

    public function sendMail($attachments, $import)
    {
        WC()->mailer()->emails["CW_Email_Notify_Import_Finished"] = include(CW()->plugin_path() . "/includes/emails/class/class-cw-email-notify-import-finished.php");

        do_action("codeswholesale_import_finished", array('attachments' => $attachments, 'import' => $import));
    }
}