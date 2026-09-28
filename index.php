<?php
$folderUpload = 'uploads/';
$error = "";
$sukses = "";


if (!is_dir($folderUpload)) {
    mkdir($folderUpload, 0755, true);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['foto']['tmp_name'];
        $fileName      = $_FILES['foto']['name'];
        $fileSize      = $_FILES['foto']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));


        $ekstensiDiizinkan = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($fileExtension, $ekstensiDiizinkan)) {
            $error = "Format file tidak diizinkan! Hanya file JPG, JPEG, PNG, GIF, atau WEBP.";
        }

        elseif ($fileSize > 2097152) {
            $error = "Ukuran file terlalu besar! Maksimal 2 MB.";
        } else {

            $namaFileBaru = time() . '_' . uniqid() . '.' . $fileExtension;
            $destPath     = $folderUpload . $namaFileBaru;


            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $sukses = "Foto berhasil diunggah!";
                header("Location: index.php?status=sukses");
                exit();
            } else {
                $error = "Gagal memindahkan file ke direktori tujuan.";
            }
        }
    } else {
        $error = "Harap pilih file foto terlebih dahulu!";
    }
}


$daftarFoto = [];
if (is_dir($folderUpload)) {
    $files = scandir($folderUpload);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $daftarFoto[] = $file;
        }
    }

    rsort($daftarFoto);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proyek 6 - Upload & Galeri Foto</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f6f9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .upload-card { background: #f8f9fa; padding: 20px; border-radius: 6px; border: 1px dashed #007bff; margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { width: 100%; padding: 8px; background: white; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        
        /* Grid Galeri */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-top: 15px; }
        .gallery-item { border: 1px solid #ddd; border-radius: 6px; overflow: hidden; background: #fff; text-align: center; }
        .gallery-item img { width: 100%; height: 140px; object-fit: cover; display: block; }
        .gallery-caption { padding: 8px; font-size: 12px; color: #666; word-break: break-all; }
    </style>
</head>
<body>

<div class="container">
    <h2>Upload & Galeri Foto</h2>

    <?php if ($error): ?>
        <div class="alert-error"><?= $error; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div class="alert-success">Foto berhasil diunggah ke galeri!</div>
    <?php endif; ?>

    <div class="upload-card">
        <!-- WAJIB menggunakan enctype="multipart/form-data" untuk upload file -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="foto">Pilih Foto (Maks 2MB - JPG, PNG, GIF, WEBP):</label>
                <input type="file" name="foto" id="foto" accept="image/*" required>
            </div>
            <button type="submit" name="upload">Unggah Foto</button>
        </form>
    </div>

    <h3>Galeri Foto Terunggah (<?= count($daftarFoto); ?>):</h3>

    <?php if (empty($daftarFoto)): ?>
        <p style="color: #888;">Belum ada foto yang diunggah.</p>
    <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($daftarFoto as $foto): ?>
                <div class="gallery-item">
                    <img src="<?= $folderUpload . $foto; ?>" alt="Foto Galeri">
                    <div class="gallery-caption"><?= htmlspecialchars($foto); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>