<?php

include "db.php";
include "functions.php";

$error = [];
$name = null;
$registered_at = null;
$total_asset = null;

if (isset($_POST['create'])) {
    $name = sanitizeData($_POST['name']);
    $registered_at = sanitizeData($_POST['registered_at']);
    $total_asset = sanitizeData($_POST['total_asset']);

    // Validation of name field
    if (empty($name)) {
        $error['name'] = "The name of the business is required.";
    }

    // Validation of registered_at field
    $date_timestamp = strtotime($registered_at);

    if (!$date_timestamp) {
        $error['registered_at'] = "Please enter a valid registration date.";
    } else if ($date_timestamp > date_timestamp_get($current_date)) {
        $error['registered_at'] = "Registration date cannot be a future date.";
    }

    // Validation of total_asset field
    $money_pattern = '/^-?\d+(?:\.\d{1,2})?$/';
    if ($total_asset <= 0) {
        $error['total_asset'] = "Please enter a total asset value greater than 0.";
    } else if (!preg_match($money_pattern, $total_asset)) {
        $error['total_asset'] = "Please enter a valid total asset value.";
    }

    // Check if there are any errors logged
    if (count($error) === 0) {
        $date_format = new DateTime($registered_at);
        $registered_at = $date_format->format('Y-m-d');

        $total_asset = doubleval($total_asset);

        $type = determineBusinessType($total_asset);

        $query = "INSERT INTO businesses (name, registered_at, total_asset, type) VALUES ('$name', '$registered_at', '$total_asset', '$type')";

        if ($connection->query($query)) {
            $connection->close();
            header("Location: index.php");
        } else {
            echo "Error: " . $connection->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Business Data</title>
</head>
<body>
    <a href="index.php">Go back to Index</a>
    <h1>Create Business Data</h1>
    <form action="add.php" method="post" style="display: flex; flex-direction: column;">
        <div>
            <label for="name">Business Name</label>
            <input type="text" name="name" id="name" value="<?= $name ?>"/>
            <?php
            if (isset($error['name'])) {
                ?>
                <p style="color: red;"><?= $error['name'] ?></p>
                <?php
            }
            ?>
        </div>

        <div>
            <label for="registered_at">Date of Registration</label>
            <input type="date" name="registered_at" id="registered_at" value="<?= $registered_at ?>"/>
            <?php
            if (isset($error['registered_at'])) {
                ?>
                <p style="color: red;"><?= $error['registered_at'] ?></p>
                <?php
            }
            ?>
        </div>

        <div>
            <label for="total_asset">Total Asset</label>
            <input type="number" name="total_asset" id="total_asset" step="any" value="<?= $total_asset ?>"/>
            <?php
            if (isset($error['total_asset'])) {
                ?>
                <p style="color: red;"><?= $error['total_asset'] ?></p>
                <?php
            }
            ?>
        </div>

        <div>
            <button name="create" type="submit">Create</button>
        </div>
    </form>
</body>
</html>