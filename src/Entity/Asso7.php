#[ORM\ManyToMany(targetEntity: Exercice::class)]
#[ORM\JoinTable(name: "Asso_7")]
private Collection $exercices;
