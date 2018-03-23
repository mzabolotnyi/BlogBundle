<?php

namespace Mauris\BlogBundle\Entity;

use Core\ApiBundle\Traits\UuidGenerator;
use Core\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Core\ApiBundle\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="blog_review")
 * @ORM\Entity()
 */
class Review
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
     * @Serializer\Groups("review")
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
     * @var string
     *
     * @Assert\NotBlank()
     * @Serializer\Groups("review")
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="Mauris\BlogBundle\Entity\ReviewComment", mappedBy="review", cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="Mauris\BlogBundle\Entity\ReviewLike", mappedBy="review", cascade={"persist", "remove"})
     */
    private $likes;

    /**
     * @Assert\Count(max="6")
     * @ORM\OneToMany(targetEntity="Mauris\BlogBundle\Entity\ReviewImage", mappedBy="review", cascade={"persist", "remove"})
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
     * @Serializer\Groups("review")
     * @return \DateTime
     */
    public function getCreatedAtVirtual()
    {
        return $this->createdAt;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("updatedAt")
     * @Serializer\Groups("review")
     * @return \DateTime
     */
    public function getUpdatedAtVirtual()
    {
        return $this->updatedAt;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("countLikes")
     * @Serializer\Groups("review")
     * @return int
     */
    public function getCountLikes()
    {
        return $this->likes->count();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("countComments")
     * @Serializer\Groups("review")
     * @return int
     */
    public function getCountComments()
    {
        return $this->comments->count();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("images")
     * @Serializer\Groups("review")
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
     * @return Review
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
     * @return Review
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
     * @return Review
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
     * Add comment.
     *
     * @param \Mauris\BlogBundle\Entity\ReviewComment $comment
     *
     * @return Review
     */
    public function addComment(\Mauris\BlogBundle\Entity\ReviewComment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param \Mauris\BlogBundle\Entity\ReviewComment $comment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeComment(\Mauris\BlogBundle\Entity\ReviewComment $comment)
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
     * @param \Mauris\BlogBundle\Entity\ReviewLike $like
     *
     * @return Review
     */
    public function addLike(\Mauris\BlogBundle\Entity\ReviewLike $like)
    {
        $this->likes[] = $like;

        return $this;
    }

    /**
     * Remove like.
     *
     * @param \Mauris\BlogBundle\Entity\ReviewLike $like
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeLike(\Mauris\BlogBundle\Entity\ReviewLike $like)
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
     * @param \Mauris\BlogBundle\Entity\ReviewImage $image
     *
     * @return Review
     */
    public function addImage(\Mauris\BlogBundle\Entity\ReviewImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image.
     *
     * @param \Mauris\BlogBundle\Entity\ReviewImage $image
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImage(\Mauris\BlogBundle\Entity\ReviewImage $image)
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

    public function isOwner(User $user)
    {
        return $user->getId() == $this->user->getId();
    }
}
