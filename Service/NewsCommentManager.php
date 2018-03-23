<?php

namespace Mauris\BlogBundle\Service;

use Core\ApiBundle\Service\BaseEntityManager;
use Mauris\BlogBundle\Entity\NewsComment;
use Symfony\Component\HttpFoundation\ParameterBag;

class NewsCommentManager extends BaseEntityManager
{
    protected $entityClass = 'Mauris\BlogBundle\Entity\NewsComment';
    protected $repositoryClass = 'MaurisBlogBundle:NewsComment';

    /**
     * @param NewsComment $comment
     * @param ParameterBag $parameters
     * @return NewsComment
     */
    protected function load($comment, ParameterBag $parameters)
    {
        if ($parameters->has('uuid')) {
            $comment->setUuid($parameters->get('uuid'));
        }

        if ($parameters->has('user')) {
            $comment->setUser($parameters->get('user'));
        }

        if ($parameters->has('news')) {
            $comment->setNews($parameters->get('news'));
        }

        if ($parameters->has('content')) {
            $comment->setContent($parameters->get('content'));
        }

        return $comment;
    }
}