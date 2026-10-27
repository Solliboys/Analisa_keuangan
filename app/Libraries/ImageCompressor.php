<?php

namespace App\Libraries;

class ImageCompressor
{
    /**
     * Kompres dan resize gambar secara otomatis
     *
     * @param string $filePath Absolute path ke file gambar
     * @param int $maxWidth Maksimal lebar gambar (default 600px)
     * @param int $maxHeight Maksimal tinggi gambar (default 600px)
     * @param int $quality Kualitas kompresi (1-100, default 80)
     * @return bool True jika berhasil atau file tidak perlu dikompres (seperti SVG)
     */
    public static function compress(string $filePath, int $maxWidth = 600, int $maxHeight = 600, int $quality = 80): bool
    {
        if (!file_exists($filePath) || !is_file($filePath)) {
            return false;
        }

        // Dapatkan informasi gambar
        $info = @getimagesize($filePath);
        if (!$info) {
            // Bisa berupa SVG atau format teks lain
            return true;
        }

        $width    = $info[0];
        $height   = $info[1];
        $mimeType = $info['mime'] ?? '';

        // Hitung dimensi baru proporsional jika melebihi batas maksimal
        $newWidth  = $width;
        $newHeight = $height;

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth  = (int)round($width * $ratio);
            $newHeight = (int)round($height * $ratio);
        }

        // Load gambar dari sumber sesuai format
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceImage = @imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $sourceImage = @imagecreatefrompng($filePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = @imagecreatefromwebp($filePath);
                }
                break;
            default:
                return true; // format lain dilewati
        }

        if (!$sourceImage) {
            return false;
        }

        // Buat canvas gambar baru
        $targetImage = imagecreatetruecolor($newWidth, $newHeight);

        // Pertahankan transparansi untuk PNG dan WEBP
        if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resample / resize gambar secara halus
        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        // Simpan gambar kembali dengan kompresi optimal
        $success = false;
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $success = imagejpeg($targetImage, $filePath, $quality);
                break;
            case 'image/png':
                // Tingkat kompresi PNG 0 (tanpa kompresi) - 9 (maksimal)
                $pngQuality = (int)round(9 - (($quality / 100) * 9));
                $pngQuality = max(0, min(9, $pngQuality));
                $success = imagepng($targetImage, $filePath, $pngQuality);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    $success = imagewebp($targetImage, $filePath, $quality);
                }
                break;
        }

        // Bersihkan resource memori
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $success;
    }
}
