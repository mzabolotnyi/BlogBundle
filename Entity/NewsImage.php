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
 * @ORM\Table(name="blog_news_image")
 * @ORM\Entity()
 */
class NewsImage
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
     * @var News
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Mauris\BlogBundle\Entity\News", inversedBy="images")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id", nullable=false)
     */
    private $news;


    public function __construct(News $news, Media $media)
    {
        $this->news = $news;
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
     * Set media.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $media
     *
     * @return NewsImage
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
     * Set news.
     *
     * @param \Mauris\BlogBundle\Entity\News $news
     *
     * @return NewsImage
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
