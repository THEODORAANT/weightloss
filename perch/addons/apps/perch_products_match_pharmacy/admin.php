<?php
if ($CurrentUser->logged_in()) {
    $this->register_app('perch_products_match_pharmacy', 'Product Pharmacy Matches', 1, 'Match products to pharmacy equivalents', '1.0');
}
