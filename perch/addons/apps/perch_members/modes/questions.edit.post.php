<?php
    require_once __DIR__.'/../questionnaire_medication_helpers.php';

    echo $HTML->title_panel([
        'heading' => $heading1,
    ], $CurrentUser);

    if ($message) echo $message;

    echo $Form->form_start();

        $product_options = [
            ['value' => 'all', 'label' => $Lang->get('All products')],
        ];
        if (function_exists('perch_questionnaire_medications')) {
            foreach (perch_questionnaire_medications() as $slug => $label) {
                if ($slug === 'none') {
                    continue;
                }

                $product_options[] = [
                    'value' => $slug,
                    'label' => $label,
                ];
            }
        }

        echo $Form->select_field('questionnaireType', 'Questionnaire type', [
            ['value'=>'first-order', 'label'=>$Lang->get('First order')],
            ['value'=>'reorder', 'label'=>$Lang->get('Re-order')]
        ], isset($details['questionnaireType'])?$details['questionnaireType']:'first-order');

        echo $Form->text_field('questionnaireSlug', 'Questionnaire name', isset($details['questionnaireSlug']) && $details['questionnaireSlug'] !== '' ? $details['questionnaireSlug'] : 'default');

        echo $Form->select_field('productSlug', 'Product', $product_options, isset($details['productSlug']) && $details['productSlug'] !== '' ? $details['productSlug'] : 'all');

        echo $Form->text_field('questionKey', 'Question key', isset($details['questionKey'])?$details['questionKey']:false);

        echo $Form->text_field('label', 'Label', isset($details['label'])?$details['label']:false);

        echo $Form->hint($Lang->get('Leave blank to reuse the question key as the form field name.'));
        echo $Form->text_field('fieldName', 'Form field name', isset($details['fieldName'])?$details['fieldName']:false);

        echo $Form->hint($Lang->get('Defines the questionnaire step slug. Leave blank to reuse the question key.'));
        echo $Form->text_field('stepSlug', 'Step slug', isset($details['stepSlug'])?$details['stepSlug']:false);

        echo $Form->select_field('type', 'Question input type', [
            ['value'=>'text','label'=>'Text field'],
            ['value'=>'textarea','label'=>'Long text field'],
            ['value'=>'radio','label'=>'Multiple choice (single answer)'],
            ['value'=>'checkbox','label'=>'Multiple choice (multiple answers)'],
            ['value'=>'hidden','label'=>'Hidden value']
        ], isset($details['type'])?$details['type']:'text');

        echo $Form->textarea_field('options', 'Options (value:label per line)', isset($details['options'])?$details['options']:'');

        echo $Form->hint($Lang->get('Provide dependency rules as JSON. Each rule should include "values" and optionally "question" and "step" keys. Leave blank if not required.'));
        echo $Form->textarea_field('dependencies', 'Dependencies (JSON)', isset($details['dependencies'])?$details['dependencies']:'', 'input-simple code');

        echo $Form->hint($Lang->get('Use numbers to control the question order shown to clients. Lower numbers display first; leave blank to add to the end.'));
        echo $Form->text_field('sort', 'Display order', isset($details['sort'])?$details['sort']:'');

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path().'/questionnaire_questions/');

    echo $Form->form_end();
?>
