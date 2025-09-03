<?php
// lib/upload.php
// Simple, cautious image uploader. Returns ['ok'=>true,'path'=>'...'] or ['ok'=>false,'msg'=>...].

function is_image_mime($tmpPath) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    return in_array($mime, $allowed);
}

function handle_image_upload($fileInput, $targetDir, $maxBytes = 3 * 1024 * 1024) {
    if (!isset($_FILES[$fileInput]) || !$_FILES[$fileInput]['name']) {
        return ['ok'=>false, 'msg'=>'No file uploaded'];
    }

    $f = $_FILES[$fileInput];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        return ['ok'=>false, 'msg'=>'Upload error code: ' . $f['error']];
    }

    if ($f['size'] > $maxBytes) {
        return ['ok'=>false, 'msg'=>'File too large (max '.($maxBytes/1024/1024).' MB)'];
    }

    if (!is_image_mime($f['tmp_name'])) {
        return ['ok'=>false, 'msg'=>'Only image files allowed (jpg, png, webp, gif)'];
    }

    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $safe = preg_replace('/[^a-z0-9_\-\.]/i', '_', pathinfo($f['name'], PATHINFO_FILENAME));
    $filename = time() . '_' . $safe . '.' . strtolower($ext);
    $dest = rtrim($targetDir, '/') . '/' . $filename;

    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        return ['ok'=>false, 'msg'=>'Failed to move uploaded file'];
    }

    // optional: set permissions
    @chmod($dest, 0644);

    // return web-accessible relative path (assume targetDir is inside project root)
    $rel = str_replace('\\','/',$dest);
    $rel = preg_replace('#^'.preg_quote(__DIR__ . '/../', '#').'#', '', $rel);

    return ['ok'=>true, 'path'=>$rel, 'filename'=>$filename];
}
