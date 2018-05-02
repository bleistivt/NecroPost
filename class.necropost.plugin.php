<?php

class NecropostPlugin extends Gdn_Plugin {

    public function settingsController_necropost_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        $sender->setHighlightRoute('settings/necropost');

        $conf = new ConfigurationModule($sender);
        $conf->initialize([
            'Necropost.Days' => [
                'Control' => 'textbox',
                'LabelCode' => 'Minimum age of a discussion (days)',
                'Default' => 356,
                'Options' => ['maxlength' => 5]
            ],
            'Necropost.Message' => [
                'Control' => 'textbox',
                'LabelCode' => 'Message',
                'Default' => $this->message(),
                'Options' => ['MultiLine' => true]
            ]
        ]);
        
        $sender->title(t('Necropost Warning'));
        $conf->renderAll();
    }


    public function discussionController_render_before($sender) {
        if (!$sender->data('Discussion') || time() - strtotime($sender->data('Discussion')->DateLastComment) < c('Necropost.Days', 365) * 86400) {
            return;
        }
        $sender->Head->addString(
            '<script type="text/javascript">
                jQuery(function($){
                    $("#Form_Comment").one("focus", ".TextBox", function() {
                        gdn.informMessage("'.$this->message().'", "Dismissable");
                    });
                });
            </script>'
        );
    }


    private function message() {
        return t(c('Necropost.Message', 'This discussion has been inactive for more than a year. Please comment only if you have something constructive to add.'));
    }

}
