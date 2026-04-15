<?php

include "db.php";

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM businesses WHERE id = '$id'";

    if ($connection->query($query)) {
        $connection->close();
        header("Location: index.php");
    } else {
        echo "Error: " . $connection->error;
    }
}

?>