<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Degree;
use App\Entity\Experience;
use App\Entity\Image;
use App\Entity\Project;
use App\Entity\Skill;
use App\Entity\Task;
use App\Entity\Technology;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        // ── User ──────────────────────────────────────────────────
        $user = new User();
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'admin123'));
        $manager->persist($user);

        // ── Images ────────────────────────────────────────────────
        $images = [];
        for ($i = 1; $i <= 10; $i++) {
            $image = new Image();
            $image->setFilename(bin2hex(random_bytes(24)) . '.webp');
            $manager->persist($image);
            $images[] = $image;
        }

        // ── Technologies ──────────────────────────────────────────
        $techNames = ['Symfony', 'React', 'Docker', 'MySQL', 'RabbitMQ'];
        $technologies = [];
        foreach ($techNames as $index => $name) {
            $tech = new Technology();
            $tech->setName($name);
            $tech->setImage($images[$index]);
            $manager->persist($tech);
            $technologies[] = $tech;
        }

        // ── Skills ────────────────────────────────────────────────
        $skillNames = [
            'PHP / Symfony',
            'React / TypeScript',
            'Docker & CI/CD',
            'MySQL / PostgreSQL',
            'REST API Design',
            'RabbitMQ / Messenger',
            'Git / GitHub',
            'AWS S3 / Cloud Storage',
        ];
        foreach ($skillNames as $skill) {
            $skillEntity = new Skill();
            $skillEntity->setDescription($skill);
            $manager->persist($skillEntity);
        }

        // ── Experiences ───────────────────────────────────────────
        $experiences = [
            [
                'company'   => 'Acme Corp',
                'job_title' => 'Full Stack Developer',
                'started'   => new \DateTime('2023-01-01'),
                'ended'     => null,
                'current'   => true,
                'tasks'     => [
                    'Developed REST APIs with Symfony',
                    'Built React frontends',
                    'Managed Docker infrastructure',
                ],
            ],
            [
                'company'   => 'Startup XYZ',
                'job_title' => 'Backend Developer',
                'started'   => new \DateTime('2021-06-01'),
                'ended'     => new \DateTime('2022-12-31'),
                'current'   => false,
                'tasks'     => [
                    'Designed and built microservices',
                    'Implemented CI/CD pipelines',
                ],
            ],
        ];

        foreach ($experiences as $exp) {
            $experience = new Experience();
            $experience->setCompanyName($exp['company']);
            $experience->setJobTitle($exp['job_title']);
            $experience->setStartedAt($exp['started']);
            $experience->setEndedAt($exp['ended']);
            $experience->setIsCurrent($exp['current']);

            foreach ($exp['tasks'] as $taskDesc) {
                $task = new Task();
                $task->setDescription($taskDesc);
                $task->setExperience($experience);
                $manager->persist($task);
            }

            $manager->persist($experience);
        }

        // ── Degrees ───────────────────────────────────────────────
        $degrees = [
            [
                'title'       => 'Bachelor of Computer Science',
                'school'      => 'University of Paris',
                'graduated'   => new \DateTime('2021-06-30'),
                'image'       => $images[5],
            ],
            [
                'title'       => 'BTS SIO SLAM',
                'school'      => 'Lycée Technique',
                'graduated'   => new \DateTime('2019-06-30'),
                'image'       => $images[6],
            ],
        ];

        foreach ($degrees as $deg) {
            $degree = new Degree();
            $degree->setTitle($deg['title']);
            $degree->setSchoolName($deg['school']);
            $degree->setGraduatedAt($deg['graduated']);
            $degree->setImage($deg['image']);
            $manager->persist($degree);
        }

        // ── Projects ──────────────────────────────────────────────
        $projects = [
            [
                'name'        => 'Portfolio API',
                'description' => 'A clean Symfony 7 REST API with JWT auth, RabbitMQ and API Platform.',
                'website_url' => 'https://api.luigigandemer.fr',
                'image'       => $images[7],
                'techs'       => [$technologies[0], $technologies[2], $technologies[3]],
            ],
            [
                'name'        => 'E-commerce Platform',
                'description' => 'Full stack e-commerce app built with Symfony and React.',
                'website_url' => null,
                'image'       => $images[8],
                'techs'       => [$technologies[0], $technologies[1], $technologies[3]],
            ],
            [
                'name'        => 'Real-time Chat App',
                'description' => 'WebSocket chat application with React frontend.',
                'website_url' => null,
                'image'       => $images[9],
                'techs'       => [$technologies[1], $technologies[4]],
            ],
        ];

        foreach ($projects as $proj) {
            $project = new Project();
            $project->setName($proj['name']);
            $project->setDescription($proj['description']);
            $project->setWebsiteUrl($proj['website_url']);
            $project->setImage($proj['image']);

            foreach ($proj['techs'] as $tech) {
                $project->addTechnology($tech);
            }

            $manager->persist($project);
        }

        // ── Contacts ──────────────────────────────────────────────
        $contacts = [
            ['name' => 'John Doe',    'email' => 'john@example.com',  'message' => 'Hello I love your portfolio!', 'status' => 'sent'],
            ['name' => 'Jane Smith',  'email' => 'jane@example.com',  'message' => 'Are you available for freelance?', 'status' => 'pending'],
        ];

        foreach ($contacts as $con) {
            $contact = new Contact();
            $contact->setName($con['name']);
            $contact->setEmail($con['email']);
            $contact->setMessage($con['message']);
            $contact->setStatus($con['status']);
            $manager->persist($contact);
        }

        $manager->flush();
    }
}
