<?php
include "db.php";
include "functions.php";

$errors = [];

$item_name = null;
$manufacturer = null;
$quantity = null;
$unit_price = null;

if (!isset($_POST['edit'])) {
    $item_id = $_GET['id'];
    $query = "SELECT * FROM supplies WHERE item_id = '$item_id';";

    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        $item_name = $row['item_name'];
        $manufacturer = $row['manufacturer'];
        $quantity = $row['quantity'];
        $unit_price = doubleval($row['unit_price']);
    } else {
        echo "Item not found.";
        die();
    }
} else {
    $item_id = sanitizeInput($_POST['item_id']);
    $item_name = sanitizeInput($_POST['item_name']);
    $manufacturer = sanitizeInput($_POST['manufacturer']);
    $quantity = sanitizeInput($_POST['quantity']);
    $unit_price = sanitizeInput($_POST['unit_price']);

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

    if ($quantity < 0 and is_numeric($quantity)) {
        $errors['quantity'] = "Quantity cannot be negative.";
    }

    if (count($errors) == 0) {

        $query = "UPDATE supplies
                    SET item_name = '$item_name',
                        manufacturer = '$manufacturer',
                        quantity = '$quantity',
                        unit_price = '$unit_price'
                    WHERE item_id = '$item_id';";

        $result = mysqli_query($connection, $query);

        if ($result) {
            $connection->close();
            header("Location: index.php");
            exit;
        } else {
            error_log("Update error: " . $connection->error);
            $connection->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <a class="back-btn" href="index.php">Go Back</a>
    <h1>Edit Item</h1>
    <form action="edit.php" method="post">
        <input type="hidden" name="item_id" value="<?= $item_id ?>">

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
            <button type="submit" name="edit">Edit Item</button>
        </div>
    </form>
</body>

</html>