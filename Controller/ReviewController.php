<?php

namespace Mauris\BlogBundle\Controller;

use Application\Sonata\MediaBundle\Exception\UploadImageException;
use Core\ApiBundle\Controller\ApiController;
use Core\ApiBundle\Exception\ForbiddenException;
use Core\ApiBundle\Exception\InvalidArgumentException;
use Core\ApiBundle\Exception\NotFoundException;
use Core\ApiBundle\Exception\ValidationException;
use Mauris\BlogBundle\Exception\LikeConflictException;
use Mauris\BlogBundle\Service\ReviewService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/review")
 */
class ReviewController extends ApiController
{
    const SERIALIZED_GROUPS = ['review', 'review_user', 'user', 'media_custom'];

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
        $entities = $this->getReviewService()->getList($request->query);
        $response = $this->handleJsonResponse($entities, self::SERIALIZED_GROUPS);

        return $response;
    }

    /**
     * Create
     *
     * @Method("POST")
     * @Route("")
     * @param Request $request
     * @return JsonResponse
     */
    public function postAction(Request $request)
    {
        try {
            $user = $this->getCurrentUser();
            $entity = $this->getReviewService()->create($user, $request->request);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (InvalidArgumentException $e) {
            $response = $this->handleInvalidParameterError($e->getArgumentName());
        } catch (UploadImageException $e) {
            $response = $this->handleBadRequestError('error.bad_request.upload_image_failed', ['reason' => $e->getMessage()]);
        } catch (ValidationException $e) {
            $response = $this->handleValidationError($e->getConstraintViolationList());
        }

        return $response;
    }

    /**
     * Update
     *
     * @Method("PUT")
     * @Route("/{uuid}")
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     */
    public function putAction(Request $request, $uuid)
    {
        try {
            $user = $this->getCurrentUser();
            $entity = $this->getReviewService()->update($uuid, $user, $request->request);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (InvalidArgumentException $e) {
            $response = $this->handleInvalidParameterError($e->getArgumentName());
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.review');
        } catch (ForbiddenException $e) {
            $response = $this->handleForbiddenError('error.forbidden');
        } catch (UploadImageException $e) {
            $response = $this->handleBadRequestError('error.bad_request.upload_image_failed', ['reason' => $e->getMessage()]);
        } catch (ValidationException $e) {
            $response = $this->handleValidationError($e->getConstraintViolationList());
        }

        return $response;
    }

    /**
     * Remove
     *
     * @Method("DELETE")
     * @Route("/{uuid}")
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $uuid)
    {
        try {
            $user = $this->getCurrentUser();
            $this->getReviewService()->remove($uuid, $user);
            $response = $this->handleJsonResponse(null);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.review');
        } catch (ForbiddenException $e) {
            $response = $this->handleForbiddenError('error.forbidden');
        }

        return $response;
    }

    /**
     * Like review
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
            $entity = $this->getReviewService()->like($uuid, $user);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.review');
        } catch (LikeConflictException $e) {
            $response = $this->handleDataConflictError('error.data_conflict.liked_already');
        }

        return $response;
    }

    /**
     * Dislike review
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
            $entity = $this->getReviewService()->dislike($uuid, $user);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.review');
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
            $entities = $this->getReviewService()->getComments($reviewUuid, $request->request);
            $response = $this->handleJsonResponse($entities, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.review');
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
            $entity = $this->getReviewService()->addComment($reviewUuid, $user, $request->request);
            $response = $this->handleJsonResponse($entity, self::SERIALIZED_GROUPS);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.review');
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
            $entity = $this->getReviewService()->updateComment($uuid, $user, $request->request);
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
            $this->getReviewService()->removeComment($uuid, $user);
            $response = $this->handleJsonResponse(null);
        } catch (NotFoundException $e) {
            $response = $this->handleNotFoundError('error.not_found.comment');
        } catch (ForbiddenException $e) {
            $response = $this->handleForbiddenError('error.forbidden');
        }

        return $response;
    }

    /**
     * @return ReviewService|object
     */
    protected function getReviewService()
    {
        return $this->container->get('mauris_blog.review_service');
    }
}