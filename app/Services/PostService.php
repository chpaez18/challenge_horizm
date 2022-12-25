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
     * Function to get post info by id 
     * 
     * @param int $id
     * 
     * @return  Post
     */
    public function getById(int $id): Post
    {
        // Get post information
        //-----------------------------------------------------------------------------------------------------------
            $posts = $this->post->select('posts.id', 'user_id', 'posts.body', 'posts.title')
            ->with(['user' => function ($query) {
                    $query->select(
                        'id',
                        'name'
                    );
                }
            ])
            ->where('posts.id', $id)
            ->firstOrFail();
        //-----------------------------------------------------------------------------------------------------------
        return $posts;
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
        $recordsToInsert = [];
        DB::beginTransaction();
        foreach ($data as $post) {
            
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
                        // If it does not exist we store the record in an array.
                        //-----------------------------------------------------------------------------------------------
                            /* if (!$this->post->create($post)) {
                                DB::rollback();
                                return false;
                            } */
                            $recordsToInsert[] = [
                                'id' => $post['id'],
                                'user_id' => $post['user_id'],
                                'title' => $post['title'],
                                'body' => $post['body'], 
                                'rating' => $post['rating']
                            ];
                        //-----------------------------------------------------------------------------------------------
                    }
                //-------------------------------------------------------------------------------------------------------
            
        }
        // we send to insert all the collected records.
        //-----------------------------------------------------------------------------------------------
            if (!$this->post->insert($recordsToInsert)) {
                DB::rollback();
                return false;
            }
        //-----------------------------------------------------------------------------------------------
        
        DB::commit();
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

    /** 
     * Function to obtain the best post of each user.
     * 
     * @return  Collection
     */
    public function getTopPosts(): Collection
    {
        // Get users list order by their posts rating
        //-----------------------------------------------------------------------------------------------------------
            $posts = $this->post->select('posts.id', 'user_id', 'posts.body', 'posts.title', DB::raw('max(rating) as rating'))
            ->with(['user' => function ($query) {
                    $query->select(
                        'id',
                        'name',
                        'email',
                        'city'
                    );
                }
            ])
            ->groupBy('user_id')
            ->orderBy('rating', 'DESC')
            ->get();
        //-----------------------------------------------------------------------------------------------------------
        
        return $posts;
    }
}
