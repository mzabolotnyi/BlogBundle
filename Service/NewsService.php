<?php

namespace Mauris\BlogBundle\Service;

use Application\Sonata\MediaBundle\Classes\MediaParameters;
use Application\Sonata\MediaBundle\Service\MediaManager;
use Core\ApiBundle\Exception\ForbiddenException;
use Core\ApiBundle\Exception\InvalidArgumentException;
use Core\ApiBundle\Exception\NotFoundException;
use Core\UserBundle\Entity\User;
use Mauris\BlogBundle\Entity\News;
use Mauris\BlogBundle\Entity\ReviewComment;
use Mauris\BlogBundle\Exception\LikeConflictException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class NewsService
{
    /** @var NewsManager */
    private $newsManager;

    /** @var NewsLikeManager */
    private $likeManager;

    /** @var NewsCommentManager */
    private $commentManager;

    /** @var MediaManager */
    private $mediaManager;


    public function __construct(ContainerInterface $container)
    {
        $this->newsManager = $container->get('mauris_blog.news_manager');
        $this->likeManager = $container->get('mauris_blog.news_like_manager');
        $this->commentManager = $container->get('mauris_blog.news_comment_manager');
        $this->mediaManager = $container->get('media_manager');
    }

    public function getList(ParameterBag $parameters)
    {
        $limit = $parameters->get('limit');
        $offset = $parameters->get('offset');

        return $this->newsManager->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function getOne($uuid)
    {
        return $this->findNews($uuid);
    }

    public function create(ParameterBag $parameters)
    {
        if ($parameters->has('images')) {
            $parameters->set('images', $this->prepareImages($parameters->get('images')));
        }

        $review = $this->newsManager->create($parameters);

        return $review;
    }

    public function update($uuid, ParameterBag $parameters)
    {
        $review = $this->findNews($uuid);

        if ($parameters->has('images')) {
            $parameters->set('images', $this->prepareImages($parameters->get('images')));
        }

        $review = $this->newsManager->update($review, $parameters);

        return $review;
    }

    public function remove($uuid)
    {
        $review = $this->findNews($uuid);

        $this->newsManager->remove($review);
    }

    public function like($uuid, User $user)
    {
        $news = $this->findNews($uuid);

        $like = $this->likeManager->findUserLike($user, $news);

        if (!is_null($like)) {
            throw new LikeConflictException();
        }

        $like = $this->likeManager->create(new ParameterBag(['user' => $user, 'news' => $news]));
        $news->addLike($like);

        return $news;
    }

    public function dislike($uuid, User $user)
    {
        $news = $this->findNews($uuid);

        $like = $this->likeManager->findUserLike($user, $news);

        if (is_null($like)) {
            throw new LikeConflictException();
        }

        $this->likeManager->remove($like);
        $news->removeLike($like);

        return $news;
    }

    public function getComments($newsUuid, ParameterBag $parameters)
    {
        $news = $this->findNews($newsUuid);

        $limit = $parameters->get('limit');
        $offset = $parameters->get('offset');

        return $this->commentManager->findBy(['news' => $news], ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function addComment($newsUuid, User $user, ParameterBag $parameters)
    {
        $news = $this->findNews($newsUuid);

        $parameters->set('user', $user);
        $parameters->set('news', $news);

        $comment = $this->commentManager->create($parameters);
        $news->addComment($comment);

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
     * @return null|News
     * @throws NotFoundException
     */
    private function findNews($uuid)
    {
        $news = $this->newsManager->findByUuid($uuid);

        if (is_null($news)) {
            throw new NotFoundException();
        }

        return $news;
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