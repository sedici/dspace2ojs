<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingFileRepository")
 */
class SettingFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $parent_file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $into_section;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authors_group;

    /**
     * @ORM\Column(type="integer")
     */
    private $limit_files;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\File", mappedBy="setting_file", fetch="EAGER")
     * 
     */
    private $files;

    public function __construct($parent_file,$into_section,$authors_group,$limit)
    {   
        $this->setAuthorsGroup($authors_group);
        $this->setIntoSection($into_section);
        $this->setLimitFiles($limit);
        $this->setParentFile($parent_file);
        $this->files = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIntoSection(): ?string
    {
        return $this->into_section;
    }

    public function setIntoSection(string $into_section): self
    {
        $this->into_section = $into_section;

        return $this;
    }

    public function getAuthorsGroup(): ?string
    {
        return $this->authors_group;
    }

    public function setAuthorsGroup(string $authors_group): self
    {
        $this->authors_group = $authors_group;

        return $this;
    }

    public function getLimitFiles(): ?int
    {
        return $this->limit_files;
    }

    public function setLimitFiles(int $limit_files): self
    {
        $this->limit_files = $limit_files;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setSettingFile($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getSettingFile() === $this) {
                $file->setSettingFile(null);
            }
        }

        return $this;
    }

    
}
