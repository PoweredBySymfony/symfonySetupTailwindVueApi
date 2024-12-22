<?php

namespace App\EventListener;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function __construct(
//Service permettant de décoder un JWT (entre autres)
        private readonly JWTTokenManagerInterface $jwtManager
    )
    {
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        $data['id'] = $user->getId();
        $data['login'] = $user->getLogin();
        $data['email'] = $user->getEmail();
        $data['username'] = $user->getLogin();
        $data['nom'] = $user->getNom();
        $data['prenom'] = $user->getPrenom();
        $data['dateDeNaissance'] = $user->getDateDeNaissance();

        //Récupération des donnés contenues de le JWT - A compléter
        //On décode le jwt qui est déjà encodé, à ce stade, afin de récupérer les informations qui nous intéressent.
        $jwt = $this->jwtManager->parse($data['token']);
        $data['token_exp'] = $jwt['exp'];

        $event->setData($data);
    }
}