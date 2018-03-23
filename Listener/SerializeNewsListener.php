<?php

namespace Mauris\BlogBundle\Listener;

use Core\UserBundle\Entity\User;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use Mauris\BlogBundle\Entity\News;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SerializeNewsListener
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        /** @var News $news */
        $news = $event->getObject();

        if ($news instanceof News) {

            /** @var SerializationContext $context */
            $context = $event->getContext();

            try {
                $groups = $context->attributes->get('groups')->get();
            } catch (\RuntimeException $e) {
                $groups = [];
            }

            if (in_array('news_user', $groups)) {
                $visitor->setData('liked', $this->checkIsLiked($news));
            }
        }
    }

    private function checkIsLiked(News $news)
    {
        $currentUser = $this->container->get('core.oauth.security_service')->getCurrentUser();
        $like = null;

        if ($currentUser instanceof User) {
            $like = $this->container->get('mauris_blog.news_like_manager')->findUserLike($currentUser, $news);
        }

        return !is_null($like);
    }
}