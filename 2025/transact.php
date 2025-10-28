<?php

include "db.php";
include "functions.php";

$errors = [];

$item_id = null;
$item_name = null;
$item_qty = null;
$unit_price = null;
$service_fee = null;
$total_amount = null;
$payment_fee = null;
$gross_total = null;
$quantity = null;
$mop = null;
$mop_name = null;

// Fetch all item IDs from supplies
$query1 = "SELECT item_id FROM supplies";
$item_list = mysqli_query($connection, $query1);

if (isset($_POST['transact'])) {
    $item_id = sanitizeInput($_POST['item_id']);
    $quantity = intval(sanitizeInput($_POST['quantity']));
    $mop = sanitizeInput($_POST['mop']);

    // Fetch all item information of chosen item
    $query2 = "SELECT * FROM supplies WHERE item_id = '$item_id'";

    $result = mysqli_query($connection, $query2);

    if ($result && count($errors) == 0) {
        $row = mysqli_fetch_assoc($result);
        $item_qty = doubleval($row['quantity']);
        $item_name = $row['item_name'];
        $unit_price = doubleval($row['unit_price']);

        // Check if quantity of item is enough for ordered quantity.
        if ($quantity <= $item_qty) {
            // Check service charge
            $counter = 1;
            $service_fee = 0.0;
            $gross_total = $quantity * $unit_price;
            while ($counter <= $quantity) {
                if ($counter <= 20) {
                    $service_fee += $unit_price * 0.04;
                } else {
                    $service_fee += $unit_price * 0.07;
                }
                $counter++;
            }

            $total_amount = $gross_total + $service_fee;

            // Check mode of payment and calculate fee
            switch($mop) {
                case 'cash':
                    $mop_name = 'Cash';
                    break;
                case 'credit':
                    $mop_name = 'Credit Card';
                    $payment_fee = $total_amount * 0.04;
                    break;
                case 'debit':
                    $mop_name = 'Debit Card';
                    $payment_fee = $total_amount * 0.04;
                    break;
                case 'check':
                    $mop_name = 'Check';
                    $payment_fee = $total_amount * 0.07;
                    break;
                default:
                    $errors['transaction'] = "Invalid mode of payment";
                    break;
            }

            $total_amount += $payment_fee;
            $item_qty -= $quantity;

            // Update quantity of chosen item
            $query3 = "UPDATE supplies SET quantity = '$item_qty' WHERE item_id = '$item_id'";

            $result = mysqli_query($connection, $query3);

            if ($result) {
                $connection->close();
            }
        } else {
            $errors['transaction'] = "There are not enough units for $item_name.";
        }

    } else {
        $errors['transaction'] = "Item not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a class="back-btn" href="index.php">Go Back</a>
    <h1>Transaction</h1>
    <form action="transact.php" method="post">
        <div class="form-control">
            <label for="item_id">Item ID:</label>
            <select name="item_id" id="item_id">
            <?php
            if ($item_list) {
                while ($row = mysqli_fetch_assoc($item_list)) {
                    ?>
                    <option value="<?= $row['item_id'] ?>"><?= $row['item_id'] ?></option>
                    <?php
                }
            }
            ?>
            </select>
        </div>
        <div class="form-control">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity"/>
        </div>
        <div class="form-control">
            <label for="mop">Mode of Payment:</label>
            <select name="mop" id="mop">
                <option value="cash">Cash</option>
                <option value="debit">Debit Card</option>
                <option value="credit">Credit Card</option>
                <option value="check">Check</option>
            </select>
        </div>
        <div class="form-buttons">
            <button type="submit" name="transact">Submit</button>
        </div>
    </form>

    <br>

    <?php
    if (isset($_POST['transact'])) {
        ?>
        <p><b>Transaction Date:</b> <?= date('F j, Y') ?></p>
        <table class="transaction-tbl">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $item_name ?></td>
                    <td><?= $quantity ?></td>
                    <td><?= "Php " . number_format($unit_price, 2) ?></td>
                    <td><?= "Php " . number_format($gross_total, 2) ?></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table class="transaction-tbl">
            <tr>
                <td><i>Service Fee</i></td>
                <td><?= "Php " . number_format($service_fee, 2) ?></td>
            </tr>
            <tr>
                <td><i>Mode of Payment</i></td>
                <td><?= $mop_name ?></td>
            </tr>
            <tr>
                <td><i>Payment Fee</i></td>
                <td><?= "Php " . number_format($payment_fee, 2) ?></td>
            </tr>
            <tr>
                <th>Total Amount Due:</th>
                <td><?= "Php " . number_format($total_amount, 2) ?></td>
            </tr>
        </table>
        <?
    } else {
        ?>
        <div><?= isset($errors['transaction']) ? $errors['transaction'] : '' ?></div>
        <?php
    }
    ?>
</body>
</html>