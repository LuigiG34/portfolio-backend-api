<?php

namespace App\Security\Voter;

use App\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    public const VIEW   = 'PROJECT_VIEW';
    public const CREATE = 'PROJECT_CREATE';
    public const EDIT   = 'PROJECT_EDIT';
    public const DELETE = 'PROJECT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($attribute, [self::CREATE])) {
            return true;
        }

        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match($attribute) {
            self::VIEW   => true, // anyone authenticated can view
            self::CREATE => in_array('ROLE_ADMIN', $user->getRoles()),
            self::EDIT   => in_array('ROLE_ADMIN', $user->getRoles()),
            self::DELETE => in_array('ROLE_ADMIN', $user->getRoles()),
            default      => false,
        };
    }
}
