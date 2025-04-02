#[ORM\ManyToMany(targetEntity: Exercice::class)]
#[ORM\JoinTable(name: "Asso_5")]
private Collection $exercices;
