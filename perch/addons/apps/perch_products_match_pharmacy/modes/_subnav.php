<?php
    echo $HTML->subnav([
        ['page' => $API->app_path(), 'label' => 'Listings'],
    ], $CurrentUser);
