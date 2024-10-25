<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class PartieConcertVoter extends Voter
{
    public const PARTIE_CONCERT_EDIT = 'PARTIE_CONCERT_EDIT';
//    public const PARTIE_CONCERT_VIEW = 'PARTIE_CONCERT_VIEW';
//  delete
    public const PARTIE_CONCERT_DELETE = 'PARTIE_CONCERT_DELETE';

    public function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::PARTIE_CONCERT_EDIT, self::PARTIE_CONCERT_DELETE])
            && $subject instanceof \App\Entity\PartieConcert;
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
            case self::PARTIE_CONCERT_DELETE:
            case self::PARTIE_CONCERT_EDIT:
                if ($subject == null) {
                    return false;
                } elseif ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_ORGANISATEUR') || $this->security->isGranted('ROLE_ARTISTE')) {
//                    je veux vérifier que l'utilisateur si il a le rôle artiste qu'il fasse bien partie de la partie concert
                    if ($this->security->isGranted('ROLE_ARTISTE')) {
                        if ($subject->getArtiste() !== $user) {
                            return false;
                        }
                    }
                    return true;
                } elseif ($user !== $subject) {
                    return false;
                }
                break;
        }

        return false;
    }
}
