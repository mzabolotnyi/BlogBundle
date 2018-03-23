<?php

namespace Mauris\BlogBundle\Service;

use Core\ApiBundle\Service\BaseEntityManager;
use Mauris\BlogBundle\Entity\ReviewComment;
use Symfony\Component\HttpFoundation\ParameterBag;

class ReviewCommentManager extends BaseEntityManager
{
    protected $entityClass = 'Mauris\BlogBundle\Entity\ReviewComment';
    protected $repositoryClass = 'MaurisBlogBundle:ReviewComment';

    /**
     * @param ReviewComment $comment
     * @param ParameterBag $parameters
     * @return ReviewComment
     */
    protected function load($comment, ParameterBag $parameters)
    {
        if ($parameters->has('uuid')) {
            $comment->setUuid($parameters->get('uuid'));
        }

        if ($parameters->has('user')) {
            $comment->setUser($parameters->get('user'));
        }

        if ($parameters->has('review')) {
            $comment->setReview($parameters->get('review'));
        }

        if ($parameters->has('content')) {
            $comment->setContent($parameters->get('content'));
        }

        return $comment;
    }
}