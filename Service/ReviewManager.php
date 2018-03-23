<?php

namespace Mauris\BlogBundle\Service;

use Core\ApiBundle\Exception\InvalidArgumentException;
use Core\ApiBundle\Service\BaseEntityManager;
use Mauris\BlogBundle\Entity\Review;
use Mauris\BlogBundle\Entity\ReviewImage;
use Symfony\Component\HttpFoundation\ParameterBag;

class ReviewManager extends BaseEntityManager
{
    protected $entityClass = 'Mauris\BlogBundle\Entity\Review';
    protected $repositoryClass = 'MaurisBlogBundle:Review';

    /**
     * @param Review $review
     * @param ParameterBag $parameters
     * @return Review
     */
    protected function load($review, ParameterBag $parameters)
    {
        if ($parameters->has('uuid')) {
            $review->setUuid($parameters->get('uuid'));
        }

        if ($parameters->has('content')) {
            $review->setContent($parameters->get('content'));
        }

        if ($parameters->has('user')) {
            $review->setUser($parameters->get('user'));
        }

        if ($parameters->has('images')) {
            $this->loadImages($review, $parameters->get('images'));
        }

        return $review;
    }

    private function loadImages(Review $review, $images)
    {
        if (!is_array($images)) {
            throw new InvalidArgumentException('images');
        }

        foreach ($review->getImages() as $reviewImage) {
            $review->removeImage($reviewImage);
            $this->em->remove($reviewImage);
        }

        foreach ($images as $media) {
            $reviewImage = new ReviewImage($review, $media);
            $review->addImage($reviewImage);
        }
    }
}