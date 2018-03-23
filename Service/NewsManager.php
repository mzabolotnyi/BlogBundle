<?php

namespace Mauris\BlogBundle\Service;

use Core\ApiBundle\Exception\InvalidArgumentException;
use Core\ApiBundle\Service\BaseEntityManager;
use Mauris\BlogBundle\Entity\News;
use Mauris\BlogBundle\Entity\NewsImage;
use Symfony\Component\HttpFoundation\ParameterBag;

class NewsManager extends BaseEntityManager
{
    protected $entityClass = 'Mauris\BlogBundle\Entity\News';
    protected $repositoryClass = 'MaurisBlogBundle:News';

    /**
     * @param News $news
     * @param ParameterBag $parameters
     * @return News
     */
    protected function load($news, ParameterBag $parameters)
    {
        if ($parameters->has('uuid')) {
            $news->setUuid($parameters->get('uuid'));
        }

        if ($parameters->has('title')) {
            $news->setTitle($parameters->get('title'));
        }

        if ($parameters->has('content')) {
            $news->setContent($parameters->get('content'));
        }

        if ($parameters->has('images')) {
            $this->loadImages($news, $parameters->get('images'));
        }

        return $news;
    }

    private function loadImages(News $news, $images)
    {
        if (!is_array($images)) {
            throw new InvalidArgumentException('images');
        }

        foreach ($news->getImages() as $newsImage) {
            $news->removeImage($newsImage);
            $this->em->remove($newsImage);
        }

        foreach ($images as $media) {
            $newsImage = new NewsImage($news, $media);
            $news->addImage($newsImage);
        }
    }
}