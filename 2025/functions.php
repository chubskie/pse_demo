<?php

// Sanitizes input
function sanitizeInput(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)));
}

// Generates Item ID
function generateItemID(string $manufacturer, string $item_name): string
{
    $id = "";

    $id = $id . substr($manufacturer, 0, 2);
    $id = $id . '-' . substr($item_name, 0, 3);
    $id = $id . '-' . date('Ymd');
    $id = $id . '-' . str_pad(random_int(1, 9999999), 7, "0", STR_PAD_LEFT);

    $id = strtoupper($id);

    return $id;
}
