<?php

namespace App\Services;

use Exception;
use App\Traits\ApiResponse;

use App\Models\Post;
use Illuminate\Http\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PostService
{
    /**
     * @var JsonPlaceHolderService
    */
    public $jsonPlaceHolderService;

    /**
     * @var Post
     */
    public $post;

    public function __construct(Post $post, JsonPlaceHolderService $jsonPlaceHolderService)
    {
        $this->post = $post;
        $this->jsonPlaceHolderService = $jsonPlaceHolderService;
    }


    /** 
     * Function to create a post in local database
     * 
     * @param Illuminate\Support\Collection  $data
     * 
     * @return  bool
     */
    public function storePost(Collection $data): bool
    {
        // We define a new structure prepared to know the final rating according to the given criteria.
        //-------------------------------------------------------------------------------------------------------------------
            $postsFormated = $this->jsonPlaceHolderService->defineWordCount($data);
        //-------------------------------------------------------------------------------------------------------------------

        foreach ($postsFormated as $post) {
            DB::beginTransaction();
                // Check if the record already exists in the local db, if it does, just update the body field.
                //-------------------------------------------------------------------------------------------------------
                    $isExists = $this->post->where('id', $post['id'])->orWhere('title', $post['title'])->first();
                    if ($isExists) {
                        $isExists->body = $post['body'];
                        if (!$isExists->update()) {
                            DB::rollback();
                            return false;
                        }
                    } else {
                        // If it doesn't exist, we create it.
                        //-----------------------------------------------------------------------------------------------
                            if (!$this->post->create($post)) {
                                DB::rollback();
                                return false;
                            }
                        //-----------------------------------------------------------------------------------------------
                    }
                //-------------------------------------------------------------------------------------------------------
            DB::commit();
        }

        return true;
    }

    /** 
     * Auxiliary function to get an array with the ids of the users that wrote a post
     * 
     * @return  Collection
     */
    public function getUsersId(): Collection
    {
        
        return $this->post->select('user_id')->distinct()->get();
    }

}
