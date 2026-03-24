<?php

namespace App\Controller;

use App\Repository\DegreeRepository;
use App\Repository\ExperienceRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use App\Repository\TechnologyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class PortfolioController extends AbstractController
{
    #[Route('/api/portfolio', name: 'api_portfolio', methods: ['GET'])]
    #[Cache(smaxage: 3600, public: true)]
    public function __invoke(
        ProjectRepository $projectRepository,
        ExperienceRepository $experienceRepository,
        SkillRepository $skillRepository,
        DegreeRepository $degreeRepository,
        TechnologyRepository $technologyRepository,
        NormalizerInterface $normalizer,
        TagAwareCacheInterface $portfolioCache,
    ): JsonResponse {
        $data = $portfolioCache->get('portfolio_data', function (ItemInterface $item) use (
            $projectRepository,
            $experienceRepository,
            $skillRepository,
            $degreeRepository,
            $technologyRepository,
            $normalizer,
        ) {
            $item->tag(['portfolio', 'projects', 'experiences', 'skills', 'degrees', 'technologies']);
            $item->expiresAfter(3600);

            return [
                'projects' => $normalizer->normalize(
                    $projectRepository->findAllWithTechnologies(),
                    null,
                    ['groups' => ['project:read']]
                ),
                'experiences' => $normalizer->normalize(
                    $experienceRepository->findAllWithTasks(),
                    null,
                    ['groups' => ['experience:read']]
                ),
                'skills' => $normalizer->normalize(
                    $skillRepository->findAll(),
                    null,
                    ['groups' => ['skill:read']]
                ),
                'degrees' => $normalizer->normalize(
                    $degreeRepository->findAll(),
                    null,
                    ['groups' => ['degree:read']]
                ),
                'technologies' => $normalizer->normalize(
                    $technologyRepository->findAll(),
                    null,
                    ['groups' => ['technology:read']]
                ),
            ];
        });

        $response = $this->json($data);
        $response->setEtag(md5($response->getContent() ?: ''));

        return $response;
    }
}
