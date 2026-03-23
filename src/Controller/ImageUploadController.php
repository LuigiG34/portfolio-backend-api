<?php

namespace App\Controller;

use App\Service\ImageUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ImageUploadController extends AbstractController
{
    #[Route('/api/images/upload', name: 'api_image_upload', methods: ['POST'])]
    public function __invoke(Request $request, ImageUploadService $imageUploadService): JsonResponse
    {
        $files = $request->files->get('files');

        // Handle both single and multiple
        if (!$files) {
            throw new BadRequestHttpException('No files uploaded.');
        }

        if (!is_array($files)) {
            $files = [$files];
        }

        $uploaded = [];
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }
            $image = $imageUploadService->upload($file);
            $uploaded[] = [
                'id' => $image->getId(),
                'filename' => $image->getFilename(),
                'url' => $request->getSchemeAndHttpHost().'/images/'.$image->getFilename(),
                'uploaded_at' => $image->getUploadedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($uploaded, 201);
    }
}
