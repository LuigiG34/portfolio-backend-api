<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImageControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(): array
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        // Check if user already exists, create if not
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => 'test_admin']);

        if (!$existingUser) {
            $user = new User();
            $user->setUsername('test_admin');
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($hasher->hashPassword($user, 'test_password'));

            $entityManager->persist($user);
            $entityManager->flush();
        }

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'test_admin',
            'password' => 'test_password',
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        return [$client, $data['token']];
    }

    private function createFakeImageFile(string $mime = 'image/jpeg'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'test_img');

        $image = imagecreatetruecolor(10, 10);
        match ($mime) {
            'image/jpeg' => imagejpeg($image, $path),
            'image/png' => imagepng($image, $path),
            'image/webp' => imagewebp($image, $path),
            default => imagejpeg($image, $path),
        };
        imagedestroy($image);

        return new UploadedFile($path, 'test.jpg', $mime, null, true);
    }

    public function testUploadWithoutTokenReturns401(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/images/upload');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUploadSingleImageReturnsArray(): void
    {
        [$client, $token] = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/api/images/upload',
            [],
            ['files' => [$this->createFakeImageFile()]],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token],
        );

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('filename', $data[0]);
        $this->assertArrayHasKey('url', $data[0]);
        $this->assertArrayHasKey('uploaded_at', $data[0]);
        $this->assertStringEndsWith('.webp', $data[0]['filename']);
    }

    public function testUploadMultipleImagesReturnsArray(): void
    {
        [$client, $token] = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/api/images/upload',
            [],
            ['files' => [
                $this->createFakeImageFile('image/jpeg'),
                $this->createFakeImageFile('image/png'),
                $this->createFakeImageFile('image/jpeg'),
            ]],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token],
        );

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        foreach ($data as $image) {
            $this->assertArrayHasKey('id', $image);
            $this->assertArrayHasKey('filename', $image);
            $this->assertArrayHasKey('url', $image);
            $this->assertStringEndsWith('.webp', $image['filename']);
        }
    }

    public function testUploadWithNoFilesReturns400(): void
    {
        [$client, $token] = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/api/images/upload',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token],
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testUploadInvalidMimeTypeReturns400(): void
    {
        [$client, $token] = $this->createAuthenticatedClient();

        $path = tempnam(sys_get_temp_dir(), 'bad_file');
        file_put_contents($path, 'not an image');
        $file = new UploadedFile($path, 'bad.pdf', 'application/pdf', null, true);

        $client->request(
            'POST',
            '/api/images/upload',
            [],
            ['files' => [$file]],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token],
        );

        $this->assertResponseStatusCodeSame(400);
    }
}
