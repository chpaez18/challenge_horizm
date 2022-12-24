<?php

namespace App\Services;

use Exception;
use App\Traits\ApiResponse;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{

    /**
     * @var JsonPlaceHolderService
    */
    public $jsonPlaceHolderService;

    /**
     * @var User
     */
    public $user;

    public function __construct(User $user, JsonPlaceHolderService $jsonPlaceHolderService)
    {
        $this->user = $user;
        $this->jsonPlaceHolderService = $jsonPlaceHolderService;
    }


    /** 
     * Function to create a user in the local database.
     * 
     * @param array  $usersId
     * 
     * @return  bool
     */
    public function storeUser(array $usersId): bool
    {
        
        foreach ($usersId as $index => $value) {
            // We obtain user information.
            //-----------------------------------------------------------------------------------------------------------
                $user = $this->jsonPlaceHolderService->getUserById($value['user_id']);
            //-----------------------------------------------------------------------------------------------------------
            DB::beginTransaction();
                $isExists = $this->user->where('id', $user['id'])->orWhere('email', $user['email'])->first();
                if ($isExists == null ) {
                    // If the user does not exist, we create it.
                    //-----------------------------------------------------------------------------------------------
                        if (!$this->user->create($user)) {
                            DB::rollback();
                            return false;
                        }
                    //-----------------------------------------------------------------------------------------------
                }
            DB::commit();
        }

        return true;
    }

}
