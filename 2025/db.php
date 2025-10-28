<?php

$connection = new mysqli('localhost', 'root', '', 'pse_demo');

if (!$connection) {
    echo "Connection failed.";
    die(mysqli_error($connection));
}