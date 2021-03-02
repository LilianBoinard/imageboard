<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Regex("/^(?=.{2,20}$)[a-zA-Z0-9 ]*(?:_[a-zA-Z0-9 ]+)*$/")
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\Regex("/^(?=.{5,200}$)[a-zA-Z0-9 ]*(?:_[a-zA-Z0-9 ]+)*$/")
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @Assert\Regex("/(https?:\/\/.*\.(?:jpeg|jpg|png|gif|svg))/i")
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAuthor(): ?int
    {
        return $this->author;
    }

    public function setAuthor(int $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getSlug(): string {
        return (new Slugify())->slugify($this->title);

    }
}
