<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function validateTacosPlace(array $data, array $files, bool $requirePhoto): array
    {
        $errors = [];
        $clean = [];

        $clean['name'] = self::sanitize((string)($data['name'] ?? ''));
        if ($clean['name'] === '' || mb_strlen($clean['name']) > 255) {
            $errors['name'] = 'Nom requis (1..255).';
        }

        $clean['description'] = self::sanitize((string)($data['description'] ?? ''));
        if ($clean['description'] === '') {
            $errors['description'] = 'Description requise.';
        } elseif (mb_strlen($clean['description']) > 5000) {
            $errors['description'] = 'Description trop longue.';
        }

        $parsedDate = self::parseDate((string)($data['date'] ?? ''));
        if ($parsedDate === null) {
            $errors['date'] = 'Date invalide.';
        } else {
            $clean['date'] = $parsedDate;
        }

        $price = filter_var($data['price'] ?? null, FILTER_VALIDATE_INT);
        if ($price === false) {
            $errors['price'] = 'Prix invalide.';
        } else {
            $clean['price'] = $price;
        }

        $lat = filter_var($data['latitude'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($lat === false || $lat < -90 || $lat > 90) {
            $errors['latitude'] = 'Latitude invalide.';
        } else {
            $clean['latitude'] = $lat;
        }

        $lng = filter_var($data['longitude'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($lng === false || $lng < -180 || $lng > 180) {
            $errors['longitude'] = 'Longitude invalide.';
        } else {
            $clean['longitude'] = $lng;
        }

        $clean['contact_name'] = self::sanitize((string)($data['contact_name'] ?? ''));
        if ($clean['contact_name'] === '' || mb_strlen($clean['contact_name']) > 255) {
            $errors['contact_name'] = 'Contact requis (max 255).';
        }

        $clean['contact_email'] = trim((string)($data['contact_email'] ?? ''));
        if ($clean['contact_email'] === '' || mb_strlen($clean['contact_email']) > 255 || !filter_var($clean['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['contact_email'] = 'Email invalide.';
        }

        if ($requirePhoto && empty($files['photo'])) {
            $errors['photo'] = 'Photo requise.';
        }

        if (!empty($files['photo'])) {
            $photoError = self::validatePhoto($files['photo']);
            if ($photoError !== null) {
                $errors['photo'] = $photoError;
            }
        }

        return [$errors, $clean];
    }

    public static function parseDate(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if ($dt instanceof \DateTime && $dt->format('Y-m-d H:i:s') === $value) {
            return $dt->format('Y-m-d H:i:s');
        }

        try {
            $dt = new \DateTime($value);
            return $dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    private static function validatePhoto(array $file): ?string
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($error !== UPLOAD_ERR_OK) {
            return 'Erreur upload.';
        }

        $size = $file['size'] ?? 0;
        if ($size > 3 * 1024 * 1024) {
            return 'Taille max 3 Mo.';
        }

        $tmp = $file['tmp_name'] ?? '';
        if ($tmp === '' || !is_file($tmp)) {
            return 'Fichier invalide.';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowed, true)) {
            return 'Type invalide (jpg/png/webp).';
        }

        return null;
    }

    private static function sanitize(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        return trim($value);
    }
}
