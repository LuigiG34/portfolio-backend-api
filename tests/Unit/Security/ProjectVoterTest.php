<?php

namespace App\Tests\Unit\Security;

use App\Entity\Project;
use App\Entity\User;
use App\Security\Voter\ProjectVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProjectVoterTest extends TestCase
{
    private ProjectVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new ProjectVoter();
    }

    private function createToken(array $roles): UsernamePasswordToken
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setRoles($roles);
        $user->setPassword('password');

        return new UsernamePasswordToken($user, 'main', $roles);
    }

    public function testAdminCanCreateProject(): void
    {
        $token = $this->createToken(['ROLE_ADMIN']);
        $result = $this->voter->vote($token, null, [ProjectVoter::CREATE]);

        $this->assertSame(1, $result); // 1 = ACCESS_GRANTED
    }

    public function testAdminCanEditProject(): void
    {
        $token = $this->createToken(['ROLE_ADMIN']);
        $project = new Project();
        $result = $this->voter->vote($token, $project, [ProjectVoter::EDIT]);

        $this->assertSame(1, $result);
    }

    public function testAdminCanDeleteProject(): void
    {
        $token = $this->createToken(['ROLE_ADMIN']);
        $project = new Project();
        $result = $this->voter->vote($token, $project, [ProjectVoter::DELETE]);

        $this->assertSame(1, $result);
    }

    public function testAdminCanViewProject(): void
    {
        $token = $this->createToken(['ROLE_ADMIN']);
        $project = new Project();
        $result = $this->voter->vote($token, $project, [ProjectVoter::VIEW]);

        $this->assertSame(1, $result);
    }

    public function testNonAdminCannotCreateProject(): void
    {
        $token = $this->createToken(['ROLE_USER']);
        $result = $this->voter->vote($token, null, [ProjectVoter::CREATE]);

        $this->assertSame(-1, $result); // -1 = ACCESS_DENIED
    }

    public function testNonAdminCannotDeleteProject(): void
    {
        $token = $this->createToken(['ROLE_USER']);
        $project = new Project();
        $result = $this->voter->vote($token, $project, [ProjectVoter::DELETE]);

        $this->assertSame(-1, $result);
    }

    public function testUnauthenticatedUserCannotDoAnything(): void
    {
        $token = $this->createToken([]);
        $project = new Project();

        $this->assertSame(-1, $this->voter->vote($token, $project, [ProjectVoter::DELETE]));
        $this->assertSame(-1, $this->voter->vote($token, $project, [ProjectVoter::EDIT]));
        $this->assertSame(-1, $this->voter->vote($token, null,    [ProjectVoter::CREATE]));
    }
}
