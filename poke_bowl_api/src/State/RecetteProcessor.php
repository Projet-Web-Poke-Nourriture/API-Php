<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RecetteProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security
    ) {
    }

    /**
     * Fonction permettant de savoir si un utilisateur peut poster une recette
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data != null) {
            $recette = $data;

            if ($this->security->getUser()->isPremium() || !$this->security->getUser()->isPremium() && count($this->security->getUser()->getRecettes()) < 10) {
                // on récupère l'objet Utilisateur lié a l'identifiant de la personne connectée
                $recette->setAuteur($this->security->getUser());
            }
        }
        // //Sauvegarde en base
        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
