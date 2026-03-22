<?php

namespace App\Service;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageUploadService
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public function __construct(
        private readonly string $uploadDir,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function upload(UploadedFile $file): Image
    {
        $this->validate($file);

        $filename = $this->generateFilename();
        $webpPath = $this->uploadDir . '/' . $filename;

        $this->convertToWebP($file, $webpPath);

        $image = new Image();
        $image->setFilename($filename);

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $image;
    }

    public function delete(Image $image): void
    {
        $path = $this->uploadDir . '/' . $image->getFilename();

        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function validate(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new BadRequestHttpException(
                'Invalid file type. Allowed: jpg, png, gif, webp.'
            );
        }

        // 5MB max
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new BadRequestHttpException(
                'File too large. Maximum size is 5MB.'
            );
        }
    }

    private function generateFilename(): string
    {
        return bin2hex(random_bytes(24)) . '.webp';
    }

    private function convertToWebP(UploadedFile $file, string $outputPath): void
    {
        $mime = $file->getMimeType();

        $source = match($mime) {
            'image/jpeg' => imagecreatefromjpeg($file->getPathname()),
            'image/png'  => imagecreatefrompng($file->getPathname()),
            'image/gif'  => imagecreatefromgif($file->getPathname()),
            'image/webp' => imagecreatefromwebp($file->getPathname()),
            default      => throw new BadRequestHttpException('Unsupported image type.'),
        };

        if ($source === false) {
            throw new BadRequestHttpException('Could not process image file.');
        }

        if (in_array($mime, ['image/png', 'image/gif'])) {
            imagepalettetotruecolor($source);
            imagealphablending($source, true);
            imagesavealpha($source, true);
        }

        imagewebp($source, $outputPath, 85);
        imagedestroy($source);
    }
}
