<?php

include "db.php";
include "functions.php";

$businesses = null;
$selectedBusiness = null;

$id = null;
$name = null;
$registered_at = null;
$total_asset = null;
$type = null;

$gross_sales = null;
$amount_due = null;

$businessQuery = "SELECT id, name FROM businesses";

$businesses = mysqli_query($connection, $businessQuery);

if (isset($_POST['calculate'])) {
    $id = sanitizeData($_POST['id']);
    $gross_sales = sanitizeData($_POST['gross_sales']);
    $selectedBusinessQuery = "SELECT * FROM businesses WHERE id = '$id'";

    $selectedBusiness = mysqli_query($connection, $selectedBusinessQuery);

    if (mysqli_num_rows($selectedBusiness) === 1) {
        $row = mysqli_fetch_assoc($selectedBusiness);

        $id = $row['id'];
        $name = $row['name'];
        $registered_at = date_create($row['registered_at']);
        $total_asset = doubleval($row['total_asset']);
        $type = $row['type'];

        // Get tax rate
        $rate = getRate($gross_sales, $type);

        // Compute months
        $months = getMonths($registered_at, $current_date);

        if ($months > 0) {
            $due = 5000;
            $due += 500 * $months;
            $due += $gross_sales * $rate;

            $amount_due = "Php " . number_format($due, 2);
        } else {
            $due = $gross_sales * $rate;

            $amount_due = "Php " . number_format($due, 2);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="index.php">Go back to index</a>
    <h1>Calculate Tax Charge</h1>
    <form action="transact.php" method="post">
        <label for="id">Business ID:</label>
        <select name="id" id="id">
            <option value="" <?= empty($id) ? 'selected' : '' ?> >Select business...</option>
            <?php
            if ($businesses) {
                while ($row = mysqli_fetch_assoc($businesses)) {
                    ?>
                    <option <?= $id === $row['id'] ? 'selected' : '' ?> value="<?= $row['id'] ?>"><?= $row['id'] . ' - ' . $row['name'] ?></option>
                    <?php
                }
            }
            ?>
        </select>

        <label for="gross_sales">Gross Sales:</label>
        <input type="number" name="gross_sales" id="gross_sales" value="<?= $gross_sales ?>" step="any"/>

        <button name="calculate" type="submit">Calculate</button>
    </form>

    <?php
    if (!empty($amount_due)) {
        ?>
        <p>Amount Due: <?= $amount_due ?></p>
        <?php
    }
    ?>
</body>
</html>