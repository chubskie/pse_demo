<?php

include "db.php";
include "functions.php";

$search = null;
$query = null;

if (isset($_GET['search']) && strlen($_GET['search']) > 0) {
    $search = sanitizeData($_GET['search']);

    $query = "SELECT * FROM businesses WHERE id LIKE '%$search%' OR name LIKE '%$search%'";
} else {
    $query = "SELECT * FROM businesses";
}

$result = mysqli_query($connection, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <style>
        th, td {
            padding: 1rem;
        }
    </style>
</head>
<body>
    <form action="index.php" method="get">
        <input type="text" name="search" id="search" value="<?= $search ?>" placeholder="Search...">
        <button type="submit">Search</button>
    </form>

    <div>
        <a href="add.php">Create Business Data</a>
        <a href="transact.php">Calculate Tax Charge</a>
    </div>

    <table>
        <thead>
            <th>ID</th>
            <th>Business Name</th>
            <th>Date of Registration</th>
            <th>Total Assets</th>
            <th>Type</th>
            <th>Actions</th>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= date_format(date_create($row['registered_at']), 'F j, Y') ?></td>
                        <td><?= "Php " . number_format(doubleval($row['total_asset']), 2) ?></td>
                        <td><?= ucfirst($row['type']) ?></td>
                        <td>
                            <a href="/edit.php?id=<?= $row['id'] ?>">Edit</a>
                            <form action="delete.php" method="post">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>"/>

                                <button name="delete" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="5">No data found.</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</body>
</html>
