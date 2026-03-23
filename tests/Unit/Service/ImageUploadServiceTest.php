<?php

namespace App\Tests\Unit\Service;

use App\Entity\Image;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageUploadServiceTest extends TestCase
{
    private string $testUploadDir;

    protected function setUp(): void
    {
        $this->testUploadDir = sys_get_temp_dir().'/portfolio_test_uploads';
        if (!is_dir($this->testUploadDir)) {
            mkdir($this->testUploadDir, 0o777, true);
        }
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->testUploadDir.'/*'));
        rmdir($this->testUploadDir);
    }

    private function createService(): ImageUploadService
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('persist');
        $entityManager->method('flush');

        return new ImageUploadService($this->testUploadDir, $entityManager);
    }

    private function createFakeImage(string $mime = 'image/jpeg'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'test_img');

        // Create a real 1x1 pixel image
        $image = imagecreatetruecolor(1, 1);
        match ($mime) {
            'image/jpeg' => imagejpeg($image, $path),
            'image/png' => imagepng($image, $path),
            'image/webp' => imagewebp($image, $path),
            default => imagejpeg($image, $path),
        };
        imagedestroy($image);

        return new UploadedFile($path, 'test.jpg', $mime, null, true);
    }

    public function testUploadReturnsImageEntity(): void
    {
        $service = $this->createService();
        $file = $this->createFakeImage();
        $image = $service->upload($file);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertStringEndsWith('.webp', $image->getFilename());
        $this->assertSame(48, strlen($image->getFilename()) - 5); // 48 chars + .webp
    }

    public function testUploadCreatesFileOnDisk(): void
    {
        $service = $this->createService();
        $file = $this->createFakeImage();
        $image = $service->upload($file);

        $this->assertFileExists($this->testUploadDir.'/'.$image->getFilename());
    }

    public function testUploadRejectsTooLargeFile(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $path = tempnam(sys_get_temp_dir(), 'big_img');
        file_put_contents($path, str_repeat('x', 6 * 1024 * 1024)); // 6MB

        $file = new UploadedFile($path, 'big.jpg', 'image/jpeg', null, true);

        $service = $this->createService();
        $service->upload($file);
    }

    public function testUploadRejectsInvalidMimeType(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $path = tempnam(sys_get_temp_dir(), 'bad_file');
        file_put_contents($path, 'not an image');

        $file = new UploadedFile($path, 'bad.pdf', 'application/pdf', null, true);

        $service = $this->createService();
        $service->upload($file);
    }

    public function testDeleteRemovesFileFromDisk(): void
    {
        $service = $this->createService();
        $file = $this->createFakeImage();
        $image = $service->upload($file);

        $path = $this->testUploadDir.'/'.$image->getFilename();
        $this->assertFileExists($path);

        $service->delete($image);

        $this->assertFileDoesNotExist($path);
    }
}
