<?php

namespace Mauris\BlogBundle\Entity;

use Core\ApiBundle\Traits\UuidGenerator;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Core\ApiBundle\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="blog_news_comment")
 * @ORM\Entity()
 */
class NewsComment
{
    use TimestampableEntity;
    use SoftDeleteableEntity;
    use UuidGenerator;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Serializer\Groups("news")
     * @ORM\Column(name="uuid", type="string", nullable=false, unique=true)
     */
    private $uuid;

    /**
     * @var User
     *
     * @Assert\NotNull()
     * @Serializer\Groups("user")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var News
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Mauris\BlogBundle\Entity\News", inversedBy="comments")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id", nullable=false)
     */
    private $news;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Serializer\Groups("news")
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;


    public function __construct()
    {
        $this->uuid = $this->generateUuid();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("createdAt")
     * @Serializer\Groups("news")
     * @return \DateTime
     */
    public function getCreatedAtVirtual()
    {
        return $this->createdAt;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("updatedAt")
     * @Serializer\Groups("news")
     * @return \DateTime
     */
    public function getUpdatedAtVirtual()
    {
        return $this->updatedAt;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function isOwner(User $user)
    {
        return $user->getId() == $this->user->getId();
    }

    /**
     * Set uuid.
     *
     * @param string $uuid
     *
     * @return NewsComment
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid.
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return NewsComment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user.
     *
     * @param \Core\UserBundle\Entity\User $user
     *
     * @return NewsComment
     */
    public function setUser(\Core\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \Core\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set news.
     *
     * @param \Mauris\BlogBundle\Entity\News $news
     *
     * @return NewsComment
     */
    public function setNews(\Mauris\BlogBundle\Entity\News $news)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * Get news.
     *
     * @return \Mauris\BlogBundle\Entity\News
     */
    public function getNews()
    {
        return $this->news;
    }
}
