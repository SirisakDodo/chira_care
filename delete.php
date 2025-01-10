<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $sql = "DELETE FROM Soldier WHERE id = $id";
    if (mysqli_query($link, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($link);
    }
} else {
    die('Invalid ID');
}
?>
