<?php
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $qrDir = __DIR__ . '/qrcodes/';

    // Ensure directories exist
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    if (!file_exists($qrDir)) mkdir($qrDir, 0777, true);

    // Get file details
    $fileName = basename($_FILES['pdf']['name']);
    $fileTmp = $_FILES['pdf']['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file type
    if ($fileExt !== 'pdf') {
        die("<p style='color:red;'>❌ Only PDF files are allowed!</p>");
    }

    // Move uploaded file to uploads directory
    $targetPath = $uploadDir . $fileName;
    if (move_uploaded_file($fileTmp, $targetPath)) {
        // Build the public file URL
        $scheme = isset($_SERVER['HTTPS']) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $fileUrl = $scheme . $host . '/uploads/' . rawurlencode($fileName);

        // Generate QR code for the file URL
        $qr = QrCode::create($fileUrl)
            ->setSize(250)
            ->setMargin(10);

        $writer = new PngWriter();
        $qrResult = $writer->write($qr);

        // Save QR code image
        $qrFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.png';
        $qrFilePath = $qrDir . $qrFileName;
        $qrResult->saveToFile($qrFilePath);

        // Display results
        echo "<h3>✅ Upload successful!</h3>";
        echo "<p><a href='$fileUrl' target='_blank'>View Uploaded PDF</a></p>";
        echo "<p>Scan this QR code to view the PDF:</p>";
        echo "<img src='/qrcodes/" . htmlspecialchars($qrFileName) . "' alt='QR Code'>";
    } else {
        echo "<p style='color:red;'>❌ Error uploading the file.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PDF → QR Code Generator</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; text-align: center; }
        form { margin-top: 20px; }
        input[type=file] { padding: 10px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #218838; }
        img { margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Upload a PDF to Generate a QR Code</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="pdf" accept="application/pdf" required><br><br>
        <button type="submit">Upload & Generate</button>
    </form>
</body>
</html>