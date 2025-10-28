<?php
include "db.php";
include "functions.php";

$errors = [];

$item_id = null;
$item_name = null;
$manufacturer = null;
$quantity = null;
$unit_price = null;

if (isset($_POST['add'])) {

    // Sanitize inputs (function is declared inside functions.php)
    $item_name = sanitizeInput($_POST['item_name']);
    $manufacturer = sanitizeInput($_POST['manufacturer']);
    $quantity = sanitizeInput($_POST['quantity']);
    $unit_price = sanitizeInput($_POST['unit_price']);

    // Fetch current date and time
    $created_at = date("Y-m-d H:i:s");

    // VALIDATIONS --------------------------------------------------------

    // Validation for item name
    if (empty($item_name)) {
        $errors['item_name'] = "Item name cannot be blank.";
    }

    // Validation for manufacturer
    if (empty($manufacturer)) {
        $errors['manufacturer'] = "Manufacturer name cannot be blank.";
    }

    // Validation for quantity
    if (is_numeric($quantity)) {
        if (intval($quantity) <= 0) {
        $errors['quantity'] = "Quantity cannot be negative nor zero.";
        }
    } else {
        $errors['quantity'] = "Quantity must be an number.";
    }

    // Validation for unit price
    if (is_numeric($unit_price)) {
        if ($unit_price <= 0) {
            $errors['unit_price'] = "Unit price cannot be negative nor zero.";
        }
    } else {
        $errors['unit_price'] = "Unit price must be a number.";
    }

    // --------------------------------------------------------------------

    // If there are no errors, proceed with database query
    if (count($errors) == 0) {
        $item_id = generateItemID($manufacturer, $item_name);

        // Prepare statement
        $statement = $connection->prepare("INSERT INTO supplies (item_id, item_name, manufacturer, quantity, unit_price, created_at) VALUE (?, ?, ?, ?, ?, ?)");

        // Check if statement prep is successful
        if ($statement) {
            /*
             * Bind parameters with the following notation:
             *      s = string
             *      i = integer
             *      d = decimal
             */
            $statement->bind_param('sssids',
                                    $item_id,
                                    $item_name,
                                    $manufacturer,
                                    $quantity,
                                    $unit_price,
                                    $created_at);

            // Attempt to execute statement. Print out potential errors.
            if ($statement->execute()) {
                $statement->close();
                header("Location: index.php");
                exit;
            } else {
                error_log("Insert error: " . $statement->error);
                $statement->close();
                die();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <a class="back-btn" href="index.php">Go Back</a>
    <h1>Add Item</h1>
    <form action="add.php" method="post">
        <div class="form-control">
            <label for="item_name">Item Name:</label>
            <input type="text" name="item_name" id="item_name" value="<?= isset($item_name) ? $item_name : '' ?>" />
            <?php
            if (isset($errors['item_name'])) {
            ?>
                <p class="error-msg"><?= $errors['item_name'] ?></p>
            <?php
            }
            ?>
        </div>
        <div class="form-control">
            <label for="manufacturer">Manufacturer:</label>
            <input type="text" name="manufacturer" id="manufacturer" value="<?= isset($manufacturer) ? $manufacturer : '' ?>" />
            <?php
            if (isset($errors['manufacturer'])) {
            ?>
                <p class="error-msg"><?= $errors['manufacturer'] ?></p>
            <?php
            }
            ?>
        </div>
        <div class="form-control">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="0" value="<?= isset($quantity) ? $quantity : '' ?>" />
            <?php
            if (isset($errors['quantity'])) {
            ?>
                <p class="error-msg"><?= $errors['quantity'] ?></p>
            <?php
            }
            ?>
        </div>

        <div class="form-control">
            <label for="unit_price">Unit Price:</label>
            <input type="number" name="unit_price" id="unit_price" step="0.01" min="0" value="<?= isset($unit_price) ? $unit_price : '' ?>" />
            <?php
            if (isset($errors['unit_price'])) {
            ?>
                <p class="error-msg"><?= $errors['unit_price'] ?></p>
            <?php
            }
            ?>
        </div>

        <div class="form-buttons">
            <a href="index.php">Cancel</a>
            <button type="submit" name="add">Add Item</button>
        </div>
    </form>
</body>

</html>