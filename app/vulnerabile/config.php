<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db_host = 'db';
$username = 'lab_user';
$password = 'lab_password';
$db_name = 'sql_injection_db';

try{
    $db_connection = new PDO("mysql:host=$db_host;dbname=$db_name", $username, $password);
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
