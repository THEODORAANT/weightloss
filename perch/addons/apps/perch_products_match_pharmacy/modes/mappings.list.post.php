<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Product Pharmacy Matches'),
        'button'  => [
            'text' => $Lang->get('Add match'),
            'link' => $API->app_path() . '/index.php?mode=mapping.edit',
            'icon' => 'core/plus'
        ],
    ], $CurrentUser);

    if (PerchUtil::count($mappings)) {
        echo '<table class="d">';
        echo '<thead><tr><th>Product ID</th><th>Pharmacy Product ID</th><th>Pharmacy Name</th><th class="action"></th><th class="action"></th></tr></thead>';
        echo '<tbody>';
        foreach ($mappings as $item) {
            $edit_link = $API->app_path() . '/index.php?mode=mapping.edit&id=' . $item['id'];
            $del_link  = $API->app_path() . '/index.php?mode=mapping.delete&id=' . $item['id'];
            echo '<tr>';
            echo '<td>' . PerchUtil::html($item['productID']) . '</td>';
            echo '<td>' . PerchUtil::html($item['pharmacy_productID']) . '</td>';
            echo '<td>' . PerchUtil::html($item['pharmacy_name']) . '</td>';
            echo '<td><a href="' . $edit_link . '" class="icon edit"></a></td>';
            echo '<td><a href="' . $del_link . '" class="icon delete" onclick="return confirm(\'Delete mapping?\')"></a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo $HTML->warning_message('No mappings found.');
    }
?>
