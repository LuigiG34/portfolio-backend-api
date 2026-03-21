<?php

namespace App\Controller;

use App\Repository\DegreeRepository;
use App\Repository\ExperienceRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use App\Repository\TechnologyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PortfolioController extends AbstractController
{
    #[Route('/api/portfolio', name: 'api_portfolio', methods: ['GET'])]
    public function __invoke(
        ProjectRepository $projectRepository,
        ExperienceRepository $experienceRepository,
        SkillRepository $skillRepository,
        DegreeRepository $degreeRepository,
        TechnologyRepository $technologyRepository,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        return $this->json([
            'projects'     => $normalizer->normalize(
                $projectRepository->findAllWithTechnologies(),
                null,
                ['groups' => ['project:read']]
            ),
            'experiences'  => $normalizer->normalize(
                $experienceRepository->findAllWithTasks(),
                null,
                ['groups' => ['experience:read']]
            ),
            'skills'       => $normalizer->normalize(
                $skillRepository->findAll(),
                null,
                ['groups' => ['skill:read']]
            ),
            'degrees'      => $normalizer->normalize(
                $degreeRepository->findAll(),
                null,
                ['groups' => ['degree:read']]
            ),
            'technologies' => $normalizer->normalize(
                $technologyRepository->findAll(),
                null,
                ['groups' => ['technology:read']]
            ),
        ]);
    }
}