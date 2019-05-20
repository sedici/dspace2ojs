<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
     /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SettingFile", inversedBy="files", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $setting_file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;
    /**
     * @ORM\Column(type="string", length=255)
     */
 
    private $date_created;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived;
     
    /**
     * @ORM\Column(type="boolean")
     */
    private $converted;

    public function __construct($current_user,$path)
    {
        $this->user = $current_user;
        $this->path= $path;
        $this->archived= false;
        $this->converted= false;
        // $this->date_create= new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getDateCreated()
    {
        return $this->date_created;
    }

    public function setDateCreated($date_created): self
    {
        $this->date_created = $date_created;

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function getParentFile(): ?string
    {
        return $this->parent_file;
    }

    public function setParentFile(string $parent_file): self
    {
        $this->parent_file = $parent_file;

        return $this;
    }

    public function getConverted(): ?bool
    {
        return $this->converted;
    }

    public function setConverted(bool $converted): self
    {
        $this->converted = $converted;

        return $this;
    }

    public function getSettingFile(): ?SettingFile
    {
        return $this->setting_file;
    }

    public function setSettingFile(?SettingFile $setting_file): self
    {
        $this->setting_file = $setting_file;

        return $this;
    }

 
}
