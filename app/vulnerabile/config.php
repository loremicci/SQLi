<?php

$db_host = 'db';
$username = 'lab_user';
$password = 'lab_password';
$db_name = 'sql_injection_db';

try{
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>