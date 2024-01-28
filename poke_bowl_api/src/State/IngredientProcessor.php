<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\IngredientRecette;
use App\Repository\IngredientRecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class IngredientProcessor implements ProcessorInterface
{
    private EntityManagerInterface $em;
    private IngredientRecetteRepository $ingredientRecetteRepository;


    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private EntityManagerInterface $entityManagerInterface,
        private IngredientRecetteRepository $ingredientRecetteRepo
    ) {
        $this->em = $entityManagerInterface;
        $this->ingredientRecetteRepository = $this->em->getRepository(IngredientRecette::class);
    }

    /**
     * Fonction permettant de supprimer un ingrédient dans les recettes correspondantes
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data != null) {
            $ingredient = $data;

            $listeIngredientRecette = $this->ingredientRecetteRepository->findBy(['ingredient' => $ingredient->getId()]);
            foreach ($listeIngredientRecette as $ingredientRecette) {
                $this->em->remove($ingredientRecette);
            }

            $this->em->flush();
        }
        // //Sauvegarde en base
        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
