<?php

try {
    $db = new PDO('mysql:host=127.0.0.1:3307;dbname=webticale', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit(); // Terminate script execution if the connection fails
}
