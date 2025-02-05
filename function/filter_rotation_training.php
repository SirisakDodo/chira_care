<?php
$rotation_filter = isset($_GET['rotation']) ? mysqli_real_escape_string($link, $_GET['rotation']) : "";
$training_filter = isset($_GET['training']) ? mysqli_real_escape_string($link, $_GET['training']) : "";

$query = "SELECT * FROM soldiers WHERE 1=1";

if (!empty($rotation_filter)) {
    $query .= " AND rotation_name = '$rotation_filter'";
}

if (!empty($training_filter)) {
    $query .= " AND training_unit_name = '$training_filter'";
}

$result = mysqli_query($link, $query);
?>