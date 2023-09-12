<?php

namespace App\Entity;

use App\Repository\BookRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $authors;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $isbn;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $publicationDate;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $meap;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private UserInterface $user;

    #[ORM\ManyToMany(targetEntity: BookCategory::class)]
    #[ORM\JoinTable(name: 'book_to_book_category')]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookToBookFormat::class, orphanRemoval: true)]
    #[ORM\JoinTable(name: 'book_to_book_format')]
    private Collection $formats;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookChapter::class)]
    private Collection $bookChapters;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->formats = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->bookChapters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

     public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function setAuthors(array $authors): self
    {
        $this->authors = $authors;

        return $this;
    }

    public function getPublicationDate(): ?DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function isMeap(): bool
    {
        return $this->meap;
    }

    public function setMeap(bool $meap): self
    {
        $this->meap = $meap;

        return $this;
    }

    /** @return Collection<BookCategory> */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, BookToBookFormat>
     */
    public function getFormats(): Collection
    {
        return $this->formats;
    }

    public function setFormats(Collection $formats): self
    {
        $this->formats = $formats;

        return $this;
    }

    public function addFormat(BookToBookFormat $format): self
    {
        if (!$this->formats->contains($format)) {
            $this->formats->add($format);
            $format->setBook($this);
        }

        return $this;
    }

    public function removeFormat(BookToBookFormat $format): self
    {
        if ($this->formats->removeElement($format)) {
            // set the owning side to null (unless already changed)
            if ($format->getBook() === $this) {
                $format->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * @param Collection $reviews
     */
    public function setReviews(Collection $reviews): void
    {
        $this->reviews = $reviews;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, BookChapter>
     */
    public function getBookChapters(): Collection
    {
        return $this->bookChapters;
    }

    public function addBookChapter(BookChapter $bookChapter): static
    {
        if (!$this->bookChapters->contains($bookChapter)) {
            $this->bookChapters->add($bookChapter);
            $bookChapter->setBook($this);
        }

        return $this;
    }

    public function removeBookChapter(BookChapter $bookChapter): static
    {
        if ($this->bookChapters->removeElement($bookChapter)) {
            // set the owning side to null (unless already changed)
            if ($bookChapter->getBook() === $this) {
                $bookChapter->setBook(null);
            }
        }

        return $this;
    }
}
