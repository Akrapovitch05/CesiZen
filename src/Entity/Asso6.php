#[ORM\ManyToMany(targetEntity: Emotion::class)]
#[ORM\JoinTable(name: "Asso_6")]
private Collection $emotions;
