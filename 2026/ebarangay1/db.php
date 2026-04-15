<?php

$connection = new mysqli('localhost', 'root', '', 'psetest1');

if (!$connection) {
    echo "Connection failed.";
    die(mysqli_error($connection));
}