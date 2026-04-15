<?php

$current_date = date_create('2025-11-11');

// Sanitizes input
function sanitizeData(string $data) {
    return trim(htmlspecialchars(strip_tags($data)));
}

function determineBusinessType(float $asset) {
    if ($asset <= 3000000) {
        return 'micro';
    } else if ($asset > 3000000 && $asset <= 15000000) {
        return 'small';
    } else if ($asset > 15000000 && $asset <= 100000000) {
        return 'medium';
    } else {
        return 'large';
    }
}

// Determine tax rate based on business type
function getRate(float $gross_sales, string $type) {
    switch($type) {
        case 'micro':
            return 0.01;
        case 'small':
            return 0.0125;
        case 'medium':
            return 0.015;
        case 'large':
            return 0.0175;
        default:
            return 0;
    }
}

// Get number of months comparing registration date and current date
function getMonths(DateTime $date, DateTime $current_date) {
    $date_year = date_format($date, 'Y');
    $current_year = date_format($current_date, "Y");
    $start_date = null;
    $start_current = null;

    if (intval($date_year) < intval($current_year)) {
        $start_date = date_create($date_year . "-02-01");
        $diff = date_diff($start_date, $current_date);
        $months = ($diff->y * 12) + ($diff->m + 1);

        return $months;
    }

    return 0;
}