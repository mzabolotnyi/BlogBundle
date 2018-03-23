<?php

namespace Mauris\BlogBundle\Service;

use Core\ApiBundle\Service\BaseEntityManager;
use Core\UserBundle\Entity\User;
use Mauris\BlogBundle\Entity\News;
use Mauris\BlogBundle\Entity\NewsLike;
use Symfony\Component\HttpFoundation\ParameterBag;

class NewsLikeManager extends BaseEntityManager
{
    protected $entityClass = 'Mauris\BlogBundle\Entity\NewsLike';
    protected $repositoryClass = 'MaurisBlogBundle:NewsLike';

    /**
     * @param User $user
     * @param News $news
     * @return null|object|NewsLike
     */
    public function findUserLike(User $user, News $news)
    {
        return $this->findOneBy([
            'user' => $user,
            'news' => $news
        ]);
    }

    /**
     * @param NewsLike $like
     * @param ParameterBag $parameters
     * @return NewsLike
     */
    protected function load($like, ParameterBag $parameters)
    {
        if ($parameters->has('user')) {
            $like->setUser($parameters->get('user'));
        }

        if ($parameters->has('news')) {
            $like->setNews($parameters->get('news'));
        }

        return $like;
    }
}