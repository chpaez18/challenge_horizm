<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use App\Http\Controllers\ApiController;
use App\Services\{JsonPlaceHolderService, PostService, UserService};

class ChallengeController extends ApiController
{
    /**
     * @var JsonPlaceHolderService
    */
    public $jsonPlaceHolderService;

    /**
     * @var PostService
    */
    public $postService;

    /**
     * @var UserService
    */
    public $userService;

    public function __construct(JsonPlaceHolderService $jsonPlaceHolderService, PostService $postService, UserService $userService)
    {
        $this->jsonPlaceHolderService = $jsonPlaceHolderService;
        $this->postService = $postService;
        $this->userService = $userService;
    }


    /**
     * Beginning of the first part of the challenge
     * 
     * @return JsonResponse
     */
    public function start(): JsonResponse
    {
        // We obtain the first 50 posts from the json place holder service.
        //-------------------------------------------------------------------------------------------------------------------
            $posts = $this->jsonPlaceHolderService->getPosts();
        //-------------------------------------------------------------------------------------------------------------------

        // We define a new structure prepared to know the final rating according to the given criteria.
        //-------------------------------------------------------------------------------------------------------------------
            $postsFormated = $this->jsonPlaceHolderService->defineWordCount($posts);
        //-------------------------------------------------------------------------------------------------------------------

        // We save the information of the posts in the local database.
        //-------------------------------------------------------------------------------------------------------------------
            $this->postService->storePost($postsFormated);
        //-------------------------------------------------------------------------------------------------------------------

        // We store the information of the users who wrote the posts.
        //-------------------------------------------------------------------------------------------------------------------
            $usersId = $this->postService->getUsersId()->toArray();
            $this->userService->storeUser($usersId);
        //-------------------------------------------------------------------------------------------------------------------
        
        return $this->successResponse('Records successfully saved.', 200);
    }
}
