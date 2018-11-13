<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Posts
 *
 * @ORM\Table(name="posts", indexes={@ORM\Index(name="posts_users_FK", columns={"user_id"}), @ORM\Index(name="posts_cathegories0_FK", columns={"cathegory_id"}), @ORM\Index(name="FK_posts_families", columns={"family_id"})})
 * @ORM\Entity
 */
class Posts
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text", length=65535, nullable=false)
     */
    private $introduction;

    /**
     * @var string|null
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="header", type="string", length=255, nullable=true)
     */
    private $header;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Families
     *
     * @ORM\ManyToOne(targetEntity="Families")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="family_id", referencedColumnName="id")
     * })
     */
    private $family;

    /**
     * @var \Cathegories
     *
     * @ORM\ManyToOne(targetEntity="Cathegories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cathegory_id", referencedColumnName="id")
     * })
     */
    private $cathegory;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Posts
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Posts
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set introduction.
     *
     * @param string $introduction
     *
     * @return Posts
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction.
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set logo.
     *
     * @param string|null $logo
     *
     * @return Posts
     */
    public function setLogo($logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo.
     *
     * @return string|null
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set header.
     *
     * @param string|null $header
     *
     * @return Posts
     */
    public function setHeader($header = null)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header.
     *
     * @return string|null
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set published.
     *
     * @param bool|null $published
     *
     * @return Posts
     */
    public function setPublished($published = null)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published.
     *
     * @return bool|null
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Posts
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified.
     *
     * @param \DateTime $modified
     *
     * @return Posts
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified.
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set family.
     *
     * @param \Families|null $family
     *
     * @return Posts
     */
    public function setFamily(\Families $family = null)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get family.
     *
     * @return \Families|null
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * Set cathegory.
     *
     * @param \Cathegories|null $cathegory
     *
     * @return Posts
     */
    public function setCathegory(\Cathegories $cathegory = null)
    {
        $this->cathegory = $cathegory;

        return $this;
    }

    /**
     * Get cathegory.
     *
     * @return \Cathegories|null
     */
    public function getCathegory()
    {
        return $this->cathegory;
    }

    /**
     * Set user.
     *
     * @param \Users|null $user
     *
     * @return Posts
     */
    public function setUser(\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \Users|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
