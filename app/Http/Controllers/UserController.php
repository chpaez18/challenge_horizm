<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use App\Http\Controllers\ApiController;
use App\Services\UserService;

class UserController extends ApiController
{
    /**
     * @var UserService
    */
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * We obtain a list of users ordered by the average rating of their post.
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try{
            
            $record = $this->userService->getList();

        } catch (Exception $exception) {
            
            throw $exception;

        }

        return $this->successResponse($record, 200);
    }
}
