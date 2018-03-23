<?php

namespace Mauris\BlogBundle\Listener;

use Core\UserBundle\Entity\User;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use Mauris\BlogBundle\Entity\Review;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SerializeReviewListener
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

        /** @var Review $review */
        $review = $event->getObject();

        if ($review instanceof Review) {

            /** @var SerializationContext $context */
            $context = $event->getContext();

            try {
                $groups = $context->attributes->get('groups')->get();
            } catch (\RuntimeException $e) {
                $groups = [];
            }

            if (in_array('review_user', $groups)) {
                $visitor->setData('liked', $this->checkIsLiked($review));
            }
        }
    }

    private function checkIsLiked(Review $review)
    {
        $currentUser = $this->container->get('core.oauth.security_service')->getCurrentUser();
        $like = null;

        if ($currentUser instanceof User) {
            $like = $this->container->get('mauris_blog.review_like_manager')->findUserLike($currentUser, $review);
        }

        return !is_null($like);
    }
}