<?php
    echo $HTML->title_panel([
        'heading' => $heading1,
    ], $CurrentUser);

    if ($message) echo $message;

    echo $Form->form_start();

        echo $Form->select_field('questionnaireType', 'Questionnaire type', [
            ['value'=>'first-order', 'label'=>$Lang->get('First order')],
            ['value'=>'reorder', 'label'=>$Lang->get('Re-order')]
        ], isset($details['questionnaireType'])?$details['questionnaireType']:'first-order');

        echo $Form->text_field('questionKey', 'Question key', isset($details['questionKey'])?$details['questionKey']:false);

        echo $Form->text_field('label', 'Label', isset($details['label'])?$details['label']:false);

        echo $Form->select_field('type', 'Field type', [
            ['value'=>'text','label'=>'Text'],
            ['value'=>'textarea','label'=>'Textarea'],
            ['value'=>'radio','label'=>'Radio'],
            ['value'=>'checkbox','label'=>'Checkbox'],
            ['value'=>'button','label'=>'Button'],
            ['value'=>'hidden','label'=>'Hidden']
        ], isset($details['type'])?$details['type']:'text');

        echo $Form->textarea_field('options', 'Options (value:label per line)', isset($details['options'])?$details['options']:'');

        echo $Form->hint($Lang->get('Use numbers to control the question order shown to clients. Lower numbers display first; leave blank to add to the end.'));
        echo $Form->text_field('sort', 'Display order', isset($details['sort'])?$details['sort']:'');

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path().'/questionnaire_questions/');

    echo $Form->form_end();
?>
