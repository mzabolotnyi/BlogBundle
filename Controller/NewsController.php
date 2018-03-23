<?php

namespace Mauris\BlogBundle\Controller;

use Core\ApiBundle\Controller\ApiController;
use Core\ApiBundle\Exception\ForbiddenException;
use Core\ApiBundle\Exception\NotFoundException;
use Core\ApiBundle\Exception\ValidationException;
use Mauris\BlogBundle\Exception\LikeConflictException;
use Mauris\BlogBundle\Service\NewsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/news")
 */
class NewsController extends ApiController
{
    const SERIALIZED_GROUPS = ['news', 'news_user', 'user', 'media_custom'];

    /**
     * Get list
     *
     * @Method("GET")
     * @Route("")
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetAction(Request $request)
    {
        $entities = $this->getNewsService()->getList($request->query);
        $response = $this->handleJsonResponse($entities, self::SERIALIZED_GROUPS);

        return $response;
    }

    /**
     * Get one
     *
     * @Method("GET")
     * @Route("/{uuid}")
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     */
    public function getAction(Request $request, $uuid)
    {
        try {
            $entity = $this->getNewsService()->getOne($uuid);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        }catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.news');
        }

        return $response;
    }

    /**
     * Like news
     *
     * @Method("POST")
     * @Route("/{uuid}/like")
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     */
    public function likeAction(Request $request, $uuid)
    {
        try {
            $user = $this->getCurrentUser();
            $entity = $this->getNewsService()->like($uuid, $user);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.news');
        } catch (LikeConflictException $e) {
            $response = $this->handleDataConflictError('error.data_conflict.liked_already');
        }

        return $response;
    }

    /**
     * Dislike news
     *
     * @Method("POST")
     * @Route("/{uuid}/dislike")
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     */
    public function dislikeAction(Request $request, $uuid)
    {
        try {
            $user = $this->getCurrentUser();
            $entity = $this->getNewsService()->dislike($uuid, $user);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.news');
        } catch (LikeConflictException $e) {
            $response = $this->handleDataConflictError('error.data_conflict.disliked_already');
        }

        return $response;
    }

    /**
     * Get comments
     *
     * @Method("GET")
     * @Route("/{reviewUuid}/comment")
     * @param Request $request
     * @param $reviewUuid
     * @return JsonResponse
     */
    public function getCommentsAction(Request $request, $reviewUuid)
    {
        try {
            $entities = $this->getNewsService()->getComments($reviewUuid, $request->request);
            $response = $this->handleJsonResponse($entities, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.news');
        }

        return $response;
    }

    /**
     * Add comment
     *
     * @Method("POST")
     * @Route("/{reviewUuid}/comment")
     * @param Request $request
     * @param $reviewUuid
     * @return JsonResponse
     */
    public function addCommentAction(Request $request, $reviewUuid)
    {
        try {
            $user = $this->getCurrentUser();
            $entity = $this->getNewsService()->addComment($reviewUuid, $user, $request->request);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.news');
        } catch (ValidationException $e) {
            $response = $this->handleValidationError($e->getConstraintViolationList());
        }

        return $response;
    }

    /**
     * Update comment
     *
     * @Method("PUT")
     * @Route("/comment/{uuid}")
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     */
    public function updateCommentAction(Request $request, $uuid)
    {
        try {
            $user = $this->getCurrentUser();
            $entity = $this->getNewsService()->updateComment($uuid, $user, $request->request);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.comment');
        } catch (ForbiddenException $e) {
            $response = $this->handleForbiddenError('error.forbidden');
        } catch (ValidationException $e) {
            $response = $this->handleValidationError($e->getConstraintViolationList());
        }

        return $response;
    }

    /**
     * Remove
     *
     * @Method("DELETE")
     * @Route("/comment/{uuid}")
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     */
    public function deleteCommentAction(Request $request, $uuid)
    {
        try {
            $user = $this->getCurrentUser();
            $this->getNewsService()->removeComment($uuid, $user);
            $response = $this->handleJsonResponse(null);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.comment');
        } catch (ForbiddenException $e) {
            $response = $this->handleForbiddenError('error.forbidden');
        }

        return $response;
    }

    /**
     * @return NewsService|object
     */
    protected function getNewsService()
    {
        return $this->container->get('mauris_blog.news_service');
    }
}