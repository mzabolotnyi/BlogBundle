<?php

namespace Mauris\BlogBundle\Entity;

use Core\ApiBundle\Traits\UuidGenerator;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Core\ApiBundle\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Application\Sonata\MediaBundle\Entity\Media;

/**
 * @ORM\Table(name="blog_review_image")
 * @ORM\Entity()
 */
class ReviewImage
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Media
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=false)
     */
    private $media;

    /**
     * @var Review
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Mauris\BlogBundle\Entity\Review", inversedBy="images")
     * @ORM\JoinColumn(name="review_id", referencedColumnName="id", nullable=false)
     */
    private $review;


    public function __construct(Review $review, Media $media)
    {
        $this->review = $review;
        $this->media = $media;
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
     * @return ReviewImage
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
     * Set media.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $media
     *
     * @return ReviewImage
     */
    public function setMedia(\Application\Sonata\MediaBundle\Entity\Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set review.
     *
     * @param \Mauris\BlogBundle\Entity\Review $review
     *
     * @return ReviewImage
     */
    public function setReview(\Mauris\BlogBundle\Entity\Review $review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review.
     *
     * @return \Mauris\BlogBundle\Entity\Review
     */
    public function getReview()
    {
        return $this->review;
    }
}
