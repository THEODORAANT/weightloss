<?php

function wl_extract_currency_amount($value)
{
    if (is_array($value)) {
        foreach ($value as $amount) {
            if (is_numeric($amount)) {
                return $amount + 0;
            }
        }
        return null;
    }

    if (is_numeric($value)) {
        return $value + 0;
    }

    return null;
}

function wl_to_bool($value)
{
    if (is_bool($value)) {
        return $value;
    }

    if (is_numeric($value)) {
        return ((int) $value) === 1;
    }

    if (is_string($value)) {
        $normalised = strtolower(trim($value));
        return in_array($normalised, ['1', 'true', 'yes', 'on'], true);
    }

    return false;
}

function wl_resolve_asset_path($path, $bucket)
{
    if (!$path) {
        return null;
    }

    if (is_array($path)) {
        return null;
    }

    if (preg_match('#^(https?:)?//#i', (string) $path)) {
        return $path;
    }

    if (isset($bucket['web_path']) && $bucket['web_path']) {
        $webPath = rtrim($bucket['web_path'], '/');
        if ($path[0] === '/') {
            return $path;
        }

        return $webPath . '/' . ltrim($path, '/');
    }

    return '/' . ltrim($path, '/');
}

function wl_format_image($image)
{
    $result = [
        'url' => null,
        'thumbnail' => null,
    ];

    if (!is_array($image)) {
        return $result;
    }

    $bucket = $image['bucket'] ?? [];

    $result['url'] = wl_resolve_asset_path(
        $image['src'] ?? ($image['_default'] ?? ($image['path'] ?? null)),
        $bucket
    );

    if (isset($image['sizes']) && is_array($image['sizes'])) {
        $preferred = ['thumb@2x', 'thumb', 'w80h80c1@1.6x', 'w80h80c1', 'square'];
        foreach ($preferred as $key) {
            if (isset($image['sizes'][$key]['path'])) {
                $result['thumbnail'] = wl_resolve_asset_path($image['sizes'][$key]['path'], $bucket);
                break;
            }
        }

        if ($result['thumbnail'] === null) {
            foreach ($image['sizes'] as $size) {
                if (isset($size['path'])) {
                    $result['thumbnail'] = wl_resolve_asset_path($size['path'], $bucket);
                    break;
                }
            }
        }
    }

    return $result;
}

function wl_format_stock_status($status)
{
    if (is_array($status)) {
        if (isset($status['processed'])) {
            $status = $status['processed'];
        } elseif (isset($status['_default'])) {
            $status = $status['_default'];
        } else {
            $status = reset($status);
        }
    }

    if ($status === null) {
        return null;
    }

    $status = trim((string) $status);

    if ($status === '') {
        return null;
    }

    $map = [
        '0' => 'unlimited',
        '1' => 'in_stock',
        '2' => 'low_stock',
        '3' => 'out_of_stock',
        '4' => 'on_order',
        '5' => 'discontinued',
    ];

    if (isset($map[$status])) {
        return $map[$status];
    }

    $normalised = strtolower(str_replace([' ', '-'], '_', $status));

    return $normalised !== '' ? $normalised : null;
}

function wl_format_variant($variant)
{
    if ($variant instanceof PerchShop_Product) {
        $variant = $variant->to_array_for_api();
    }

    if (!is_array($variant)) {
        return null;
    }

    $dose = isset($variant['productVariantDesc']) ? trim((string) $variant['productVariantDesc']) : '';
    if ($dose === '' && isset($variant['title'])) {
        $dose = trim((string) $variant['title']);
    }

    $doseLabel = $dose;
    if ($dose !== '') {
        $lowerDose = strtolower($dose);
        if ($dose === '2.5mg' || strpos($lowerDose, '0.25') !== false) {
            $doseLabel = $dose . ' (Starting Dose)';
        }
    }

    $stockLevel = $variant['stock_level'] ?? ($variant['stockLevel'] ?? null);
    if ($stockLevel === '' || $stockLevel === null) {
        $stockLevel = null;
    } elseif (is_numeric($stockLevel)) {
        $stockLevel = (int) $stockLevel;
    } else {
        $stockLevel = is_numeric((string) $stockLevel) ? (int) $stockLevel : null;
    }

    return [
        'id' => isset($variant['productID']) ? (int) $variant['productID'] : null,
        'sku' => isset($variant['sku']) ? (string) $variant['sku'] : '',
        'dose' => $dose,
        'dose_label' => $doseLabel,
        'price' => wl_extract_currency_amount($variant['price'] ?? ($variant['regular_price'] ?? null)),
        'sale_price' => wl_extract_currency_amount($variant['sale_price'] ?? null),
        'stock_level' => $stockLevel,
        'stock_status' => wl_format_stock_status($variant['stock_status'] ?? ($variant['stockStatus'] ?? null)),
    ];
}

function wl_format_product($product)
{
    if ($product instanceof PerchShop_Product) {
        $product = $product->to_array_for_api();
    }

    if (!is_array($product)) {
        return null;
    }

    $variantOutput = [];
    if (isset($product['variants']) && is_array($product['variants'])) {
        foreach ($product['variants'] as $variant) {
            $formatted = wl_format_variant($variant);
            if ($formatted !== null) {
                $variantOutput[] = $formatted;
            }
        }
    }

    $status = $product['status'] ?? ($product['productStatus'] ?? null);
    if (is_numeric($status)) {
        $status = ((int) $status === 1) ? 'active' : 'inactive';
    } elseif (is_string($status)) {
        $status = strtolower(trim($status));
    }

    $basePrice = wl_extract_currency_amount($product['price'] ?? ($product['regular_price'] ?? null));
    $salePrice = wl_extract_currency_amount($product['sale_price'] ?? null);
    $isOnSale = wl_to_bool($product['on_sale'] ?? false)
        || wl_to_bool($product['sale_pricing'] ?? false)
        || ($salePrice !== null && $basePrice !== null && $salePrice < $basePrice);

    return [
        'id' => isset($product['productID']) ? (int) $product['productID'] : null,
        'sku' => isset($product['sku']) ? (string) $product['sku'] : '',
        'title' => isset($product['title']) ? (string) $product['title'] : '',
        'slug' => isset($product['slug']) ? (string) $product['slug'] : (isset($product['productSlug']) ? (string) $product['productSlug'] : ''),
        'description' => isset($product['description']) ? (string) $product['description'] : '',
        'image' => wl_format_image($product['image'] ?? null),
        'base_price' => $basePrice,
        'sale_price' => $salePrice,
        'is_on_sale' => $isOnSale,
        'requires_shipping' => wl_to_bool($product['requires_shipping'] ?? false),
        'status' => $status,
        'variants' => $variantOutput,
    ];
}
