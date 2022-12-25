<?php

namespace App\Services;

use Exception;
use App\Models\User;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * @return  Collection
     */
    public function getList(): Collection
    {
        // Get users list order by their posts rating
        //-----------------------------------------------------------------------------------------------------------
            $user = $this->user->select('users.id','users.name','users.email', 'users.city', DB::raw('(SELECT avg(rating) FROM posts WHERE posts.user_id = users.id) as posts_average'))
            ->with(['posts' => function ($query) {
                    $query->select(
                        'id',
                        'user_id',
                        'title',
                        'body',
                        'rating'
                    );
                    $query->orderBy('id', 'ASC');
                }
            ])
            ->orderBy('posts_average', 'DESC')
            ->get();

        //-----------------------------------------------------------------------------------------------------------
        
        return $user;
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
        $recordsToInsert = [];
        DB::beginTransaction();

        // Get users list.
        //-----------------------------------------------------------------------------------------------------------
            $users = $this->jsonPlaceHolderService->getUsers();
        //-----------------------------------------------------------------------------------------------------------

        
        foreach ($usersId as $index => $value) {
            // We obtain user information.
            //-----------------------------------------------------------------------------------------------------------
                $user = $users->where('id', $value['user_id'])->first();
                //$user = $this->jsonPlaceHolderService->getUserById($value['user_id']);
            //-----------------------------------------------------------------------------------------------------------
            
            $isExists = $this->user->where('id', $user['id'])->orWhere('email', $user['email'])->first();
            if ($isExists == null ) {
                // If it does not exist we store the record in an array.
                //-----------------------------------------------------------------------------------------------
                    /* if (!$this->user->create($user)) {
                        DB::rollback();
                        return false;
                    } */
                    $recordsToInsert[] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'password' => bcrypt('123456'), 
                        'city' => $user['address']['city']
                    ];
                //-----------------------------------------------------------------------------------------------
            }
            
        }
        // we send to insert all the collected records.
        //-----------------------------------------------------------------------------------------------
            if (!$this->user->insert($recordsToInsert)) {
                DB::rollback();
                return false;
            }
        //-----------------------------------------------------------------------------------------------
        
        DB::commit();
        return true;
    }
}
