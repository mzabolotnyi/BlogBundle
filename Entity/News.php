<?php

namespace Mauris\BlogBundle\Entity;

use Core\ApiBundle\Traits\UuidGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Core\ApiBundle\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="blog_news")
 * @ORM\Entity()
 */
class News
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
     * @var string
     *
     * @Assert\NotBlank()
     * @Serializer\Groups("news")
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Serializer\Groups("news")
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="Mauris\BlogBundle\Entity\NewsComment", mappedBy="news", cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="Mauris\BlogBundle\Entity\NewsLike", mappedBy="news", cascade={"persist", "remove"})
     */
    private $likes;

    /**
     * @Assert\Count(max="6")
     * @ORM\OneToMany(targetEntity="Mauris\BlogBundle\Entity\NewsImage", mappedBy="news", cascade={"persist", "remove"})
     */
    private $images;

    public function __construct()
    {
        $this->uuid = $this->generateUuid();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->images = new ArrayCollection();
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
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("countLikes")
     * @Serializer\Groups("news")
     * @return int
     */
    public function getCountLikes()
    {
        return $this->likes->count();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("countComments")
     * @Serializer\Groups("news")
     * @return int
     */
    public function getCountComments()
    {
        return $this->comments->count();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("images")
     * @Serializer\Groups("news")
     * @return array
     */
    public function getImagesVirtual()
    {
        $images = [];

        foreach ($this->images as $reviewImage) {
            $images[] = $reviewImage->getMedia();
        }

        return $images;
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

    /**
     * Set uuid.
     *
     * @param string $uuid
     *
     * @return News
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
     * Set title.
     *
     * @param string $title
     *
     * @return News
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
     * Set content.
     *
     * @param string $content
     *
     * @return News
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
     * Add comment.
     *
     * @param \Mauris\BlogBundle\Entity\NewsComment $comment
     *
     * @return News
     */
    public function addComment(\Mauris\BlogBundle\Entity\NewsComment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param \Mauris\BlogBundle\Entity\NewsComment $comment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeComment(\Mauris\BlogBundle\Entity\NewsComment $comment)
    {
        return $this->comments->removeElement($comment);
    }

    /**
     * Get comments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add like.
     *
     * @param \Mauris\BlogBundle\Entity\NewsLike $like
     *
     * @return News
     */
    public function addLike(\Mauris\BlogBundle\Entity\NewsLike $like)
    {
        $this->likes[] = $like;

        return $this;
    }

    /**
     * Remove like.
     *
     * @param \Mauris\BlogBundle\Entity\NewsLike $like
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeLike(\Mauris\BlogBundle\Entity\NewsLike $like)
    {
        return $this->likes->removeElement($like);
    }

    /**
     * Get likes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Add image.
     *
     * @param \Mauris\BlogBundle\Entity\NewsImage $image
     *
     * @return News
     */
    public function addImage(\Mauris\BlogBundle\Entity\NewsImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image.
     *
     * @param \Mauris\BlogBundle\Entity\NewsImage $image
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImage(\Mauris\BlogBundle\Entity\NewsImage $image)
    {
        return $this->images->removeElement($image);
    }

    /**
     * Get images.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }
}
