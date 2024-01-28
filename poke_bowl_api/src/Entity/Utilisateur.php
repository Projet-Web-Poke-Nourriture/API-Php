<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\State\UtilisateurProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity('login', message : "Ce login est déjà pris!")]
#[UniqueEntity('adresseEmail', message : "Cet email est déjà pris!")]
#[ApiResource(
    operations: [
        new Get(),
        new Post(
            processor: UtilisateurProcessor::class, 
            validationContext: ["groups" => ["Default", "utilisateur:create"]],
            denormalizationContext: ["groups" => ["utilisateur:create"]]),
        new Delete(),
        new Patch(
            processor: UtilisateurProcessor::class, 
            validationContext: ["groups" => ["Default", "utilisateur:update"]], 
            denormalizationContext: ["groups" => ["utilisateur:update"]]
        ),
        new GetCollection(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ["groups" => ["ingredientRecette:read", "utilisateur:read"]]
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['utilisateur:read', 'ingredientRecette:read', 'utilisateur:create', 'utilisateur:update', "recette:read"])]
    private ?int $id = null;

    #[Assert\NotBlank(groups: ["utilisateur:create"])]
    #[Assert\NotNull(groups: ["utilisateur:create"])]
    #[Assert\Length(
        min: 4, 
        max: 20, 
        minMessage: 'Il faut au moins 4 caractères!', 
        maxMessage: 'Votre login est trop long ! (+20 caractères)'
    )]
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["ingredientRecette:read", "utilisateur:read", "utilisateur:create", "recette:read"])]
    private ?string $login = null;

    #[ORM\Column]
    #[ApiProperty(writable : false)]
    #[Groups(["ingredientRecette:read", "utilisateur:read", "utilisateur:create", "recette:read"])]
    private array $roles = [];
    
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[ApiProperty(writable : false, readable: false)]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ["utilisateur:create"])]
    #[Assert\NotNull(groups: ["utilisateur:create"])]
    #[Groups(['utilisateur:create'])]
    #[Assert\Regex(pattern: '#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,30}$#', message: 'Le mot de passe ne respecte pas les règles.')]
    #[Assert\Length(
        min: 8,
        max: 30,
        minMessage: 'Le mot de passe est trop court',
        maxMessage: 'Le mot de passe est trop long'
    )]
    private ?string $plainPassword = null;

    #[Assert\NotBlank(groups: ["utilisateur:create"])]
    #[Assert\NotNull(groups: ["utilisateur:create"])]
    #[Assert\Email(message: "L'adresse email {{ value }} n'est pas valide.")]
    #[ORM\Column(length: 255, unique: true)]
    #[Groups(["ingredientRecette:read", "utilisateur:read", "utilisateur:create", "utilisateur:update", "recette:read"])]
    private ?string $adresseEmail = null;

    #[ORM\Column]
    #[ApiProperty(writable : false)]
    #[Groups(["ingredientRecette:read", "utilisateur:read", "recette:read"])]
    private bool $premium = false;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Recette::class, orphanRemoval: true)]
    private Collection $recettes;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role) : void {
        if(!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole($role) : void {
        $index = array_search($role, $this->roles);
        //array_search renvoie soit l'index (la clé) soit false is rien n'est trouvé
        //Préciser le !== false est bien nécessaire, car si le role se trouve à l'index 0, utiliser un simple if($index) ne vérifie pas le type! Et donc, si l'index retournait est 0, la condition ne passerait pas...!
        if ($index !== false) {
            unset($this->roles[$index]);
        }
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getAdresseEmail(): ?string
    {
        return $this->adresseEmail;
    }

    public function setAdresseEmail(string $adresseEmail): static
    {
        $this->adresseEmail = $adresseEmail;

        return $this;
    }

    public function isPremium(): ?bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): static
    {
        $this->premium = $premium;

        return $this;
    }

    /**
     * @return Collection<int, Recette>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): static
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->setAuteur($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getAuteur() === $this) {
                $recette->setAuteur(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}