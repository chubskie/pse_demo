<?php

include "db.php";

if (isset($_POST['delete'])) {
    $item_id = htmlspecialchars(strip_tags(trim($_POST['id'])));

    $statement = $connection->prepare("DELETE FROM supplies WHERE item_id = ?");

    if ($statement) {
        $statement->bind_param("s", $item_id);

        if ($statement->execute()) {
            $statement->close();
            header("Location: index.php");
            exit;
        } else {
            error_log("Delete error: " . $statement->error);
            echo "Failed to delete";
            $statement->close();
        }
    } else {
        error_log($connection->error);
    }
}