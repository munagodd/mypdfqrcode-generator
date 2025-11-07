<?php
require_once 'phpqrcode/qrlib.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $uploadDir = 'uploads/';
    $qrDir = 'qrcodes/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    if (!is_dir($qrDir)) mkdir($qrDir, 0777, true);

    $fileName = uniqid() . '.pdf';
    $uploadFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadFile)) {
        $url = "https://your-render-app.onrender.com/view.php?id=$fileName";
        $qrFile = $qrDir . $fileName . '.png';
        QRcode::png($url, $qrFile, QR_ECLEVEL_L, 4);

        echo "<h3>QR Code Generated!</h3>";
        echo "<p>Scan this QR to view your PDF:</p>";
        echo "<img src='$qrFile' alt='QR Code'><br><br>";
        echo "<a href='$url' target='_blank'>Open PDF directly</a>";
    } else {
        echo "Failed to upload PDF.";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
  <h2>Upload a PDF</h2>
  <input type="file" name="pdf" accept="application/pdf" required><br><br>
  <button type="submit">Upload and Generate QR</button>
</form>