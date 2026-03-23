<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;

class ImageUploadOpenApiDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated,
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        // Upload endpoint
        $openApi->getPaths()->addPath('/api/images/upload', new Model\PathItem(
            post: new Model\Operation(
                operationId: 'uploadImages',
                tags: ['Images'],
                summary: 'Upload one or multiple images',
                description: 'Uploads one or multiple images, converts them to WebP. Requires ROLE_ADMIN.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'files[]' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'string',
                                            'format' => 'binary',
                                        ],
                                        'description' => 'One or multiple image files',
                                    ],
                                ],
                                'required' => ['files[]'],
                            ],
                        ],
                    ])
                ),
                responses: [
                    '201' => new Model\Response(
                        description: 'Images uploaded successfully',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer'],
                                            'filename' => ['type' => 'string'],
                                            'url' => ['type' => 'string'],
                                            'uploaded_at' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                    '400' => new Model\Response(description: 'Invalid file or no file uploaded'),
                    '401' => new Model\Response(description: 'Unauthorized'),
                ],
                security: [['bearerAuth' => []]],
            )
        ));

        return $openApi;
    }
}
