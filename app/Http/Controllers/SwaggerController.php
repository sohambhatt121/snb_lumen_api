<?php

/**
 *
 * @SWG\Definition(definition="ErrorResponse", type="object", required={"messages"},
 *         @SWG\Property(property="status", type="string"),
 *         @SWG\Property(property="messages", type="array", @SWG\Items(type="string")),
 *         @SWG\Property(property="trace", type="array", @SWG\Items(type="string"))
 * )
 */

 /**
  * @SWG\Definition(definition="PagingData", type="object",
  *     @SWG\Property(property="currentPage", type="integer", format="int64"),
  *     @SWG\Property(property="nextPage", type="integer", format="int64"),
  *     @SWG\Property(property="prevPage", type="integer", format="int64"),
  *     @SWG\Property(property="totalItems", type="integer", format="int64"),
  *     @SWG\Property(property="totalPages", type="integer", format="int64"),
  *     @SWG\Property(property="firstPage", type="integer", format="int64"),
  *     @SWG\Property(property="lastPage", type="integer", format="int64"),
  *
  * )
 */

namespace App\Http\Controllers;

class SwaggerController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $swagger = \Swagger\scan(base_path('app/'));
        return response()->json($swagger);
    }
}
