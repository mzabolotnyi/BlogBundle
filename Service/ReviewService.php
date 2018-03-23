<?php

namespace Mauris\BlogBundle\Service;

use Application\Sonata\MediaBundle\Classes\MediaParameters;
use Application\Sonata\MediaBundle\Service\MediaManager;
use Core\ApiBundle\Exception\ForbiddenException;
use Core\ApiBundle\Exception\InvalidArgumentException;
use Core\ApiBundle\Exception\NotFoundException;
use Core\UserBundle\Entity\User;
use Mauris\BlogBundle\Entity\Review;
use Mauris\BlogBundle\Entity\ReviewComment;
use Mauris\BlogBundle\Exception\LikeConflictException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class ReviewService
{
    /** @var ReviewManager */
    private $reviewManager;

    /** @var ReviewLikeManager */
    private $likeManager;

    /** @var ReviewCommentManager */
    private $commentManager;

    /** @var MediaManager */
    private $mediaManager;


    public function __construct(ContainerInterface $container)
    {
        $this->reviewManager = $container->get('mauris_blog.review_manager');
        $this->likeManager = $container->get('mauris_blog.review_like_manager');
        $this->commentManager = $container->get('mauris_blog.review_comment_manager');
        $this->mediaManager = $container->get('media_manager');
    }

    public function getList(ParameterBag $parameters)
    {
        $limit = $parameters->get('limit');
        $offset = $parameters->get('offset');

        return $this->reviewManager->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function create(User $user, ParameterBag $parameters)
    {
        if ($parameters->has('images')) {
            $parameters->set('images', $this->prepareImages($parameters->get('images')));
        }

        $parameters->set('user', $user);

        $review = $this->reviewManager->create($parameters);

        return $review;
    }

    public function update($uuid, User $user, ParameterBag $parameters)
    {
        $review = $this->findReview($uuid);

        if (!$review->isOwner($user)) {
            throw new ForbiddenException();
        }

        if ($parameters->has('images')) {
            $parameters->set('images', $this->prepareImages($parameters->get('images')));
        }

        $review = $this->reviewManager->update($review, $parameters);

        return $review;
    }

    public function remove($uuid, User $user)
    {
        $review = $this->findReview($uuid);

        if (!$review->isOwner($user)) {
            throw new ForbiddenException();
        }

        $this->reviewManager->remove($review);
    }

    public function like($uuid, User $user)
    {
        $review = $this->findReview($uuid);

        $like = $this->likeManager->findUserLike($user, $review);

        if (!is_null($like)) {
            throw new LikeConflictException();
        }

        $like = $this->likeManager->create(new ParameterBag(['user' => $user, 'review' => $review]));
        $review->addLike($like);

        return $review;
    }

    public function dislike($uuid, User $user)
    {
        $review = $this->findReview($uuid);

        $like = $this->likeManager->findUserLike($user, $review);

        if (is_null($like)) {
            throw new LikeConflictException();
        }

        $this->likeManager->remove($like);
        $review->removeLike($like);

        return $review;
    }

    public function getComments($reviewUuid, ParameterBag $parameters)
    {
        $review = $this->findReview($reviewUuid);

        $limit = $parameters->get('limit');
        $offset = $parameters->get('offset');

        return $this->commentManager->findBy(['review' => $review], ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function addComment($reviewUuid, User $user, ParameterBag $parameters)
    {
        $review = $this->findReview($reviewUuid);

        $parameters->set('user', $user);
        $parameters->set('review', $review);

        $comment = $this->commentManager->create($parameters);
        $review->addComment($comment);

        return $comment;
    }

    public function updateComment($uuid, User $user, ParameterBag $parameters)
    {
        $comment = $this->findComment($uuid);

        if (!$comment->isOwner($user)) {
            throw new ForbiddenException();
        }

        $comment = $this->commentManager->update($comment, $parameters);

        return $comment;
    }

    public function removeComment($uuid, User $user)
    {
        $comment = $this->findComment($uuid);

        if (!$comment->isOwner($user)) {
            throw new ForbiddenException();
        }

        $comment = $this->commentManager->remove($comment);

        return $comment;
    }

    /**
     * @param $uuid
     * @return null|Review
     * @throws NotFoundException
     */
    private function findReview($uuid)
    {
        $review = $this->reviewManager->findByUuid($uuid);

        if (is_null($review)) {
            throw new NotFoundException();
        }

        return $review;
    }

    /**
     * @param $uuid
     * @return null|ReviewComment
     * @throws NotFoundException
     */
    private function findComment($uuid)
    {
        $comment = $this->commentManager->findByUuid($uuid);

        if (is_null($comment)) {
            throw new NotFoundException();
        }

        return $comment;
    }

    private function prepareImages($imagesParam)
    {
        if (!is_array($imagesParam)) {
            throw new InvalidArgumentException('images');
        }

        $images = [];

        foreach ($imagesParam as $imageData) {
            if (isset($imageData['data'])) {
                $media = $this->mediaManager->createImageFromBase64($imageData['data'], MediaParameters::CONTEXT_IMAGE_NEWS);
            } elseif (isset($imageData['url'])) {
                $media = $this->mediaManager->createImageFromUrl($imageData['url'], MediaParameters::CONTEXT_IMAGE_NEWS);
            } else {
                throw new InvalidArgumentException('images');
            }
            $images[] = $media;
        }

        return $images;
    }
}