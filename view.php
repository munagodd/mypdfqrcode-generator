<?php
$id = $_GET['id'] ?? '';
$file = "uploads/" . basename($id);

if (file_exists($file)) {
    header('Content-Type: application/pdf');
    readfile($file);
} else {
    echo "File not found.";
}
?>