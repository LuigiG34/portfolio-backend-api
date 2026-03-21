<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;

class PortfolioOpenApiDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/api/portfolio', new Model\PathItem(
            get: new Model\Operation(
                operationId: 'getPortfolio',
                tags: ['Portfolio'],
                summary: 'Get all portfolio data',
                description: 'Public endpoint. Returns all projects, experiences, skills, degrees and technologies in one request.',
                responses: [
                    '200' => new Model\Response(
                        description: 'Portfolio data retrieved successfully',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'projects' => [
                                            'type' => 'array',
                                            'items' => ['$ref' => '#/components/schemas/Project']
                                        ],
                                        'experiences' => [
                                            'type' => 'array',
                                            'items' => ['$ref' => '#/components/schemas/Experience']
                                        ],
                                        'skills' => [
                                            'type' => 'array',
                                            'items' => ['$ref' => '#/components/schemas/Skill']
                                        ],
                                        'degrees' => [
                                            'type' => 'array',
                                            'items' => ['$ref' => '#/components/schemas/Degree']
                                        ],
                                        'technologies' => [
                                            'type' => 'array',
                                            'items' => ['$ref' => '#/components/schemas/Technology']
                                        ],
                                    ]
                                ]
                            ]
                        ])
                    )
                ]
            )
        ));

        return $openApi;
    }
}