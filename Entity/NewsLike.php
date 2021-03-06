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
 * @ORM\Table(name="blog_news_like", uniqueConstraints={
 *        @UniqueConstraint(name="news_like_index",
 *            columns={"news_id", "user_id"})
 *    })
 * @ORM\Entity()
 * @UniqueEntity(fields={"news", "user"})
 */
class NewsLike
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
     * @var News
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Mauris\BlogBundle\Entity\News", inversedBy="likes")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id", nullable=false)
     */
    private $news;


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
     * @return NewsLike
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
     * @return NewsLike
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
