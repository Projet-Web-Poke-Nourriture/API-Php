<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    /**
     * @param JWTCreatedEvent $event
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();

        //Insertion de données 
        $payload["id"] = $event->getUser()->getId();
        $payload["login"] = $event->getUser()->getLogin();
        $payload["premium"] = $event->getUser()->isPremium();
        $payload["adresseEmail"] = $event->getUser()->getAdresseEmail();

        $event->setData($payload);
    }
}