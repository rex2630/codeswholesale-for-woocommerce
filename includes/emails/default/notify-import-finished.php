<?php

/**
 * CW_Email_Notify_Import_Finished_Default
 */
class CW_Email_Notify_Import_Finished_Default extends CW_DefaultEmailDataModel
{
    public function __construct() {
        $this->id       = "notify_import_finished";
        $this->title    = "CodesWholesale import finished";
        $this->heading  = "Watch out. Your import finished.";
        $this->subject  = "Watch out. Your import finished.";
        $this->content  = "<p>Hello,</p> <br>"
                . "<p>The import has been finished.</p>"
                . "<br><p>Best,</p>";
        
        $this->hint     = "{title}";
    }
}