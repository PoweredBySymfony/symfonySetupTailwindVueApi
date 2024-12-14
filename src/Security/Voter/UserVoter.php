<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends Voter
{
    public const USER_EDIT = 'UTILISATEUR_EDIT';
    public const USER_DELETE = 'UTILISATEUR_DELETE';

    public function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::USER_EDIT, self::USER_DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::USER_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                if ($subject == null) {
                    return false;
                }
                elseif ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                elseif ($user !== $subject){
                    return false;
                }
                return true;
            case self::USER_DELETE:
                if ($subject == null) {
                    return false;
                }
                elseif ($this->security->isGranted('ROLE_ADMIN')){
                    return true;
                }
                elseif ($user !== $subject){
                    return false;
                }
                return true;
        }

        return false;
    }
}
