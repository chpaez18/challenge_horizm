<?php

namespace App\Services;

use Exception;
use App\Traits\ApiResponse;

use App\Models\{Post, User};
use Illuminate\Http\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;


class JsonPlaceHolderService
{

    public $baseUrl;

    public function __construct()
    {
        //When instantiating the class, we define the main url of the json placeholder api.
        $this->baseUrl = 'https://jsonplaceholder.typicode.com';
    }

    /**
     * Function to get a list of the first 50 posts.
     * 
     * @return  Collection
     */
    public function getPosts(): Collection
    {
        
        //We get the api response.
        //-------------------------------------------------------------------------------------------------------------------
            //We use try to control the connection exception to the external service.
            try {
                $url = $this->baseUrl."/posts?_limit=50";
                $response = Http::withHeaders(['Content-Type' => 'application/json'])->withOptions(["verify"=>false])->get($url)->json();
            } catch (Exception $exception) {
                throw $exception;
            }
        //-------------------------------------------------------------------------------------------------------------------
        
        //we return a collection of the response from the endpoint.
        return collect($response);
    }

    /**
     * Function to get a list of the users.
     * 
     * @return  Collection
     */
    public function getUsers(): Collection
    {
        
        //We get the api response.
        //-------------------------------------------------------------------------------------------------------------------
            //We use try to control the connection exception to the external service.
            try {
                $url = $this->baseUrl.'/users';
                $response = Http::withHeaders(['Content-Type' => 'application/json'])->withOptions(['verify'=>false])->get($url)->json();
            } catch (Exception $exception) {
                throw $exception;
            }
        //-------------------------------------------------------------------------------------------------------------------
        
        //we return a collection of the response from the endpoint.
        return collect($response);
    }

    /**
     * Funcion para obtener la informacion de un usuario por id
     * @param int $id
     * 
     * @return  array
     */
    public function getUserById(int $id): array
    {
        
        //We get the api response.
        //-------------------------------------------------------------------------------------------------------------------
            //We use try to control the connection exception to the external service.
            try {
                $url = $this->baseUrl.'/users'.'/'.$id;
                $response = Http::withHeaders(['Content-Type' => 'application/json'])->withOptions(["verify"=>false])->get($url)->json();
            } catch (Exception $exception) {
                throw $exception;
            }
        //-------------------------------------------------------------------------------------------------------------------
        
        //We obtain from the answer only the data we are interested in.
        //-------------------------------------------------------------------------------------------------------------------
            $user = [];
            $user['id'] = $response['id'];
            $user['name'] = $response['name'];
            $user['email'] = $response['email'];
            $user['city'] = $response['address']['city'];
            $user['password'] = bcrypt('123456');
        //-------------------------------------------------------------------------------------------------------------------

        //return user information.
        return $user;
    }

    /**
     * Funcion para definir una estructura preparada con la cantidad de palabras en los campos title y body
     * 
     * @param Collection $posts
     * 
     * @return  Collection
     */
    public function defineWordCount($posts): Collection
    {

        // Iterate over all posts.
        //-------------------------------------------------------------------------------------------------------------------
            $finalArray = [];
            foreach ($posts as $index => $post) {

                // We calculate the final rating according to the given criteria.
                //-----------------------------------------------------------------------------------------------------------
                    $titlePoints = (str_word_count($post['title']) * 2);
                    $bodyPoints = str_word_count($post['body']);
                    $finalRating = ($titlePoints + $bodyPoints);
                //-----------------------------------------------------------------------------------------------------------

                // We modify the original structure by adding the rating already calculated.
                //-----------------------------------------------------------------------------------------------------------
                    $finalArray[$index] = [
                        'user_id' => $post['userId'],
                        'id' => $post['id'],
                        'title' => $post['title'],
                        'body' => $post['body'],
                        'rating' => $finalRating
                    ];
                //-----------------------------------------------------------------------------------------------------------
           
            }
            
        //-------------------------------------------------------------------------------------------------------------------
        
        //we return a collection of the already assembled array.
        return collect($finalArray);
    }

}
