<?php

namespace Mauris\BlogBundle\Service;

use Core\ApiBundle\Service\BaseEntityManager;
use Core\UserBundle\Entity\User;
use Mauris\BlogBundle\Entity\Review;
use Mauris\BlogBundle\Entity\ReviewLike;
use Symfony\Component\HttpFoundation\ParameterBag;

class ReviewLikeManager extends BaseEntityManager
{
    protected $entityClass = 'Mauris\BlogBundle\Entity\ReviewLike';
    protected $repositoryClass = 'MaurisBlogBundle:ReviewLike';

    /**
     * @param User $user
     * @param Review $review
     * @return null|object|ReviewLike
     */
    public function findUserLike(User $user, Review $review)
    {
        return $this->findOneBy([
            'user' => $user,
            'review' => $review
        ]);
    }

    /**
     * @param ReviewLike $like
     * @param ParameterBag $parameters
     * @return ReviewLike
     */
    protected function load($like, ParameterBag $parameters)
    {
        if ($parameters->has('user')) {
            $like->setUser($parameters->get('user'));
        }

        if ($parameters->has('review')) {
            $like->setReview($parameters->get('review'));
        }

        return $like;
    }
}