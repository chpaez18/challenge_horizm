<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use App\Http\Controllers\ApiController;
use App\Services\PostService;

class PostController extends ApiController
{
    /**
     * @var PostService
    */
    public $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Gets the best post from each user.
     * 
     * @return JsonResponse
     */
    public function top(): JsonResponse
    {
        try{
            
            $record = $this->postService->getTopPosts();

        } catch (Exception $exception) {
            
            throw $exception;

        }

        return $this->successResponse($record, 200);
    }

    /**
     * Get specific post by id
     * 
     * @param int $id
     * 
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try{
            
            $record = $this->postService->getById($id);

        } catch (Exception $exception) {
            
            throw $exception;

        }

        return $this->successResponse($record, 200);
    }
}
