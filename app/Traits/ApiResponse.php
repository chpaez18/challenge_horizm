<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Fractalistic\ArraySerializer;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
	/**
	 * Function to return a successful response in json format with a defined structure.
	 */
	protected function successResponse($data, $code)
	{
		return response()->json([
			'responseCode' => $code, 
			'data' => $data
		], $code);
	}
	
	/**
	 * Function to return a message with the defined json structure.
	 */
	protected function showMessage($message, $code)
	{
		return response()->json([
			'responseCode' => $code, 
			'data' => $message
		], $code);
	}
}