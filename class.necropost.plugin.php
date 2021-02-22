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
        
        $sender->title(Gdn::translate('Necropost Warning'));
        $conf->renderAll();
    }


    public function discussionController_render_before($sender) {
        $dateLastComment = $sender->data('Discussion.DateLastComment');
        if (!$dateLastComment || time() - strtotime($dateLastComment) < Gdn::config('Necropost.Days', 365) * 86400) {
            return;
        }
        $sender->Head->addString(
            '<script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    var textbox = document.querySelector("#Form_Comment .TextBox");
                    if (textbox) {
                        textbox.addEventListener("focus", function() {
                            gdn.informMessage('.json_encode($this->message()).', "Dismissable");
                        }, {once: true});
                    }
                });
            </script>'
        );
    }


    private function message() {
        return Gdn::translate(Gdn::config(
            'Necropost.Message',
            'This discussion has been inactive for more than a year. Please comment only if you have something constructive to add.
        '));
    }

}
