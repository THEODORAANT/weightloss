<?php
    $Form = $API->get('Form');
    $DB   = $API->get('DB');
    $message = false;

    $id = false;
    if (PerchUtil::get('id')) {
        $id = (int) PerchUtil::get('id');
        $sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'products_match_pharmacy WHERE id='.$DB->pdb($id).' LIMIT 1';
        $details = $DB->get_row($sql);
    } else {
        $details = ['productID'=>'','pharmacy_productID'=>'','pharmacy_name'=>''];
    }

    if ($Form->submitted()) {
        $postvars = ['productID','pharmacy_productID','pharmacy_name'];
        $data = $Form->receive($postvars);

        if ($id) {
            $sql = 'UPDATE '.PERCH_DB_PREFIX.'products_match_pharmacy SET productID='.$DB->pdb((int)$data['productID']).', pharmacy_productID='.$DB->pdb($data['pharmacy_productID']).', pharmacy_name='.$DB->pdb($data['pharmacy_name']).' WHERE id='.$DB->pdb($id).' LIMIT 1';
            $DB->execute($sql);
            $message = $HTML->success_message('Mapping updated.');
        } else {
            $sql = 'INSERT INTO '.PERCH_DB_PREFIX.'products_match_pharmacy(productID, pharmacy_productID, pharmacy_name) VALUES ('.$DB->pdb((int)$data['productID']).','.$DB->pdb($data['pharmacy_productID']).','.$DB->pdb($data['pharmacy_name']).')';
            $DB->execute($sql);
            $id = $DB->insert_id();
            PerchUtil::redirect($API->app_path().'/index.php?mode=mapping.edit&id='.$id.'&created=1');
        }
        $details = $data;
    }

    if (PerchUtil::get('created')) {
        $message = $HTML->success_message('Mapping created.');
    }
