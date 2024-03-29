<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Table(name="`user`")
 */
class User implements UserInterface, \JsonSerializable
{

    public function jsonSerialize()
    {
        $array = [
            'id' => $this->getId(),
            'projects' => $this->getProjects(),
            'situations' => $this->getSituations(),
            'username' => $this->getUsername(),
        ];
        return $array;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="user_id")
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity=Situation::class, mappedBy="user_id")
     */
    private $situations;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="user")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity=InvitationToken::class, mappedBy="user")
     */
    private $invitationTokens;

    /**
     * @ORM\OneToMany(targetEntity=UserAccess::class, mappedBy="user", orphanRemoval=true)
     */
    private $userAccesses;

    /**
     * @ORM\OneToMany(targetEntity=Notebook::class, mappedBy="user")
     */
    private $notebooks;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="user")
     */
    private $notes;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->situations = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->invitationTokens = new ArrayCollection();
        $this->userAccesses = new ArrayCollection();
        $this->notebooks = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setUserId($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getUserId() === $this) {
                $task->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Situation[]
     */
    public function getSituations(): Collection
    {
        return $this->situations;
    }

    public function addSituation(Situation $situation): self
    {
        if (!$this->situations->contains($situation)) {
            $this->situations[] = $situation;
            $situation->setUserId($this);
        }

        return $this;
    }

    public function removeSituation(Situation $situation): self
    {
        if ($this->situations->contains($situation)) {
            $this->situations->removeElement($situation);
            // set the owning side to null (unless already changed)
            if ($situation->getUserId() === $this) {
                $situation->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setUser($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getUser() === $this) {
                $project->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InvitationToken[]
     */
    public function getInvitationTokens(): Collection
    {
        return $this->invitationTokens;
    }

    public function addInvitationToken(InvitationToken $invitationToken): self
    {
        if (!$this->invitationTokens->contains($invitationToken)) {
            $this->invitationTokens[] = $invitationToken;
            $invitationToken->setUser($this);
        }

        return $this;
    }

    public function removeInvitationToken(InvitationToken $invitationToken): self
    {
        if ($this->invitationTokens->contains($invitationToken)) {
            $this->invitationTokens->removeElement($invitationToken);
            // set the owning side to null (unless already changed)
            if ($invitationToken->getUser() === $this) {
                $invitationToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserAccess[]
     */
    public function getUserAccesses(): Collection
    {
        return $this->userAccesses;
    }

    public function addUserAccess(UserAccess $userAccess): self
    {
        if (!$this->userAccesses->contains($userAccess)) {
            $this->userAccesses[] = $userAccess;
            $userAccess->setUser($this);
        }

        return $this;
    }

    public function removeUserAccess(UserAccess $userAccess): self
    {
        if ($this->userAccesses->removeElement($userAccess)) {
            // set the owning side to null (unless already changed)
            if ($userAccess->getUser() === $this) {
                $userAccess->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notebook[]
     */
    public function getNotebooks(): Collection
    {
        return $this->notebooks;
    }

    public function addNotebook(Notebook $notebook): self
    {
        if (!$this->notebooks->contains($notebook)) {
            $this->notebooks[] = $notebook;
            $notebook->setUser($this);
        }

        return $this;
    }

    public function removeNotebook(Notebook $notebook): self
    {
        if ($this->notebooks->removeElement($notebook)) {
            // set the owning side to null (unless already changed)
            if ($notebook->getUser() === $this) {
                $notebook->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }
}
