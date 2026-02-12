<?php
    $DB = $API->get('DB');
    $sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'products_match_pharmacy ORDER BY id ASC';
    $mappings = $DB->get_rows($sql);
