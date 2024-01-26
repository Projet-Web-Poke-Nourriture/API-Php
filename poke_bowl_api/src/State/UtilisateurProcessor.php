<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UtilisateurProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher
    )
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if($data->getPlainPassword() != null){
            $utilisateur = $data;
            $plainPassword = $data->getPlainPassword();

            //On chiffre le mot de passe en clair
            $hashed = $this->passwordHasher->hashPassword($utilisateur, $plainPassword);
            
            //On met à jour l'attribut "password" de l'utilisateur
            $utilisateur->setPassword($hashed);
        }
        //Sauvegarde en base
        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
