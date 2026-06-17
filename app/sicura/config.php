<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DIFESA (Principio dei Minimi Privilegi e Isolamento del Database):
// 1. Usiamo un database separato ("sql_injection_secure_db") rispetto all'app vulnerabile.
//    In questo modo, attacchi distruttivi sull'app vulnerabile non intaccano questi dati.
// 2. Usiamo un utente specifico ("lab_user_secure") a cui sono stati tolti i permessi
//    di DROP e DELETE. Anche in caso di vulnerabilità 0-day, i dati non potrebbero essere cancellati.
$db_host = 'db';
$username = 'lab_user_secure';
$password = 'lab_password_secure';
$db_name = 'sql_injection_secure_db';

try{
    $db_connection = new PDO("mysql:host=$db_host;dbname=$db_name", $username, $password);
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>

