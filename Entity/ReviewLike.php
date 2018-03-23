<?php

namespace Mauris\BlogBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Core\ApiBundle\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="blog_review_like", uniqueConstraints={
 *        @UniqueConstraint(name="review_like_index",
 *            columns={"review_id", "user_id"})
 *    })
 * @ORM\Entity()
 * @UniqueEntity(fields={"review", "user"})
 */
class ReviewLike
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
     * @var User
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Review
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Mauris\BlogBundle\Entity\Review", inversedBy="likes")
     * @ORM\JoinColumn(name="review_id", referencedColumnName="id", nullable=false)
     */
    private $review;


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
     * Set user.
     *
     * @param \Core\UserBundle\Entity\User $user
     *
     * @return ReviewLike
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
     * Set review.
     *
     * @param \Mauris\BlogBundle\Entity\Review $review
     *
     * @return ReviewLike
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
