<?php

include "db.php";

$query = "SELECT * FROM supplies";

$result = mysqli_query($connection, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>

<body>
    <div class="body-header">
        <div class="project-info">
            <p><b>Basic Inventory Management System</b></p>
            <p><b>Prepared by:</b> Chazz Manubay</p>
            <p>October 28, 2025</p>
        </div>
        <div>
        <a href="add.php">Add Item</a>
        <a href="transact.php">Transaction</a>
        </div>
    </div>
    <div>
        <table class="body-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Item ID</th>
                    <th>Item Name</th>
                    <th>Manufacturer</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                        <tr>
                            <td><b><?= $row['item_id'] ?></b></td>
                            <td><?= $row['item_name'] ?></td>
                            <td><?= $row['manufacturer'] ?></td>
                            <td><?= number_format(doubleval($row['quantity']), 0) ?></td>
                            <td><?= "Php " . number_format(round(doubleval($row['unit_price']), 2), 2) ?></td>
                            <td><?= date_format(date_create($row['created_at']), "Y-m-d h:i:s A") ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?= $row['item_id'] ?>">Edit</a>
                                    <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        <input type="hidden" name="id" value="<?= $row['item_id'] ?>" />
                                        <button type="submit" name="delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>