<?php
    $DB = $API->get('DB');
    $id = (int) PerchUtil::get('id');
    if ($id) {
        $sql = 'DELETE FROM '.PERCH_DB_PREFIX.'products_match_pharmacy WHERE id=' . $DB->pdb($id) . ' LIMIT 1';
        $DB->execute($sql);
    }
    PerchUtil::redirect($API->app_path());
