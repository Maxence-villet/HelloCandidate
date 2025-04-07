<?php
class FileUploader {
    const MAX_FILE_SIZE = 5242880; // 5MB
    const ALLOWED_MIME_TYPES = ['application/pdf'];

    public static function uploadPdf($fileInput, $userId) {
        self::validateUpload($fileInput);
        
        $destination = self::generateDestinationPath($userId);
        self::moveUploadedFile($fileInput['tmp_name'], $destination);
        
        return '/uploads/cover_letters/' . basename($destination);
    }

    private static function validateUpload($fileInput) {
        if (!isset($fileInput['error']) || is_array($fileInput['error'])) {
            throw new RuntimeException('Paramètres de fichier invalides');
        }

        if ($fileInput['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::getUploadError($fileInput['error']));
        }

        if ($fileInput['size'] > self::MAX_FILE_SIZE) {
            throw new RuntimeException('Le fichier ne doit pas dépasser 5MB');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fileInput['tmp_name']);
        
        if (!in_array($mime, self::ALLOWED_MIME_TYPES)) {
            throw new RuntimeException('Seuls les fichiers PDF sont acceptés');
        }
    }

    private static function getUploadError($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux',
            UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux',
            UPLOAD_ERR_PARTIAL => 'Fichier partiellement uploadé',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier téléchargé',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'Erreur d\'écriture sur le disque',
            UPLOAD_ERR_EXTENSION => 'Upload arrêté par une extension PHP'
        ];
        return $errors[$errorCode] ?? 'Erreur inconnue lors de l\'upload';
    }

    private static function generateDestinationPath($userId) {
        $uploadDir = __DIR__ . '/../public/uploads/cover_letters/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = sprintf(
            'user_%d_%s.pdf',
            $userId,
            bin2hex(random_bytes(8))
        );

        return $uploadDir . $filename;
    }

    private static function moveUploadedFile($tmpPath, $destination) {
        if (!move_uploaded_file($tmpPath, $destination)) {
            throw new RuntimeException('Échec du téléversement du fichier');
        }
    }
}