<?php


namespace App\Entity;


class Post
{
    private int $id;

    private string $title;

    private string $cover;

    private string $date;

    private string $text;

    private string $slug;

    private User $user;

    private Category $category;

    private array $tags;


    /**
     * Post entity constructor.
     *
     * @param int $id
     * @param string $title
     * @param string $cover
     * @param string $date
     * @param string $text
     * @param string $slug
     * @param User $user
     * @param Category $category
     * @param array $tags
     */
    public function __construct(int $id, string $title, string $cover, string $date, string $text, string $slug, User $user, Category $category, array $tags)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setCover($cover);
        $this->setDate($date);
        $this->setText($text);
        $this->setSlug($slug);
        $this->setUser($user);
        $this->setCategory($category);
        $this->setTags($tags);
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    /**
     * @return string
     */
    public function getCover(): string
    {
        return $this->cover;
    }


    /**
     * @param string $cover
     */
    public function setCover(string $cover): void
    {
        $this->cover = $cover;
    }


    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }


    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }


    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }


    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }


    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }


    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }


    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }


    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}