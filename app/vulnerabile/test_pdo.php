<?php
require_once 'config.php';

$sql = "SELECT 1; UPDATE grades SET grade = 99 WHERE student_id = 4;";
try {
    $stmt = $db_connection->query($sql);
    echo "Query executed.\n";
    // $stmt->nextRowset(); // Let's see if it updates without this
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$stmt2 = $db_connection->query("SELECT grade FROM grades WHERE student_id = 4 LIMIT 1");
$res = $stmt2->fetchColumn();
echo "Grade is now: " . $res . "\n";
