<?php

/**
 *@SWG\Definition(type="object", definition="ImageList",
 *   @SWG\Property(
 *     property="items",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/Image")
 *   )
 *)
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Laracasts\Validation\FormValidator;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use File;
use unlink;

class ImageController extends Controller
{
    public $currentPage;
    public $perPage;
    public $orderByName;
    public $orderBy;
    public $condition = [];

    public function getModelName()
    {
        return 'Image';
    }

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->perPage = $request->query('perPage');
        $this->currentPage = $request->query('currentPage');
        $this->orderByName = $request->query('orderByName');
        $this->orderBy = strtoupper($request->query('orderBy'));
        $this->condition = $request->query();
        
        unset($this->condition['perPage'],$this->condition['currentPage'],$this->condition['orderByName'],$this->condition['orderBy']);
    }

    /**
     * @SWG\Post(
     *     tags={"Image"},
     *     path="/image",
     *     description="Creates image",
     *     summary="Create image",
     *     operationId="CreateImage",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *       in="formData",
     *       name="event_id",
     *       type="integer",
     *       description="Event ID",
     *       required=false
     *     ),
     *     @SWG\Parameter(
     *       in="formData",
     *       name="title",
     *       type="string",
     *       description="Image Title",
     *       required=false
     *     ),
     *     @SWG\Parameter(
     *       in="formData",
     *       name="description",
     *       type="string",
     *       description="Image description",
     *       required=false
     *     ),
     *     @SWG\Parameter(
     *       in="formData",
     *       name="url",
     *       type="file",
     *       description="Image URL",
     *       required=true
     *     ),
     *     consumes={
     *         "multipart/form-data"
     *     },
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */

    public function create()
    {
        $expectedForCreate = [
            'event_id' => 'numeric|required',
            'title' => 'nullable',
            'description' => 'nullable'
        ];
        $expectedForFile = ['url' => 'required|image'];
        
        if ($this->isTokenValid())
        {  
            $errors = $this->validation($this->requestData, $expectedForCreate);
            $errorFile = $this->validation(['url' => $this->requestFile], $expectedForFile);
            if($errors->isEmpty() && $errorFile->isEmpty())
            {

                try
                {
                    $destinationPath = base_path()."/public/event_images/";
                    $fileName = $this->requestFile->getClientOriginalName();
                    $this->requestFile->move($destinationPath, $fileName);
                    $this->requestData += array('url' => "/event_images/".$fileName);

                    $event = \App\Event::where('event_id', $this->requestData['event_id'])->first();
                    if($event == null)
                    {
                        return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Evant not exist']);               
                    }

                    $result = parent::create();
                    return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Record Created Successfully']);
                }
                catch(Exception $e)
                {
                    return $this->sendResponse(500, ['status' => 'ERROR', 'messages' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                }
            }
            else
            {
                return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => implode("'\n'", $errors->all())]);
            }  
        }
        else
        {
            return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Unauthorized']);
        }
    }


    /**
     * @SWG\Get(
     *     tags={"Image"},
     *     path="/image",
     *     description="Returns list of images",
     *     summary="Get images",
     *     operationId="GetImages",
     *     @SWG\Parameter(
     *         description="Per Page",
     *         format="int64",
     *         in="query",
     *         name="perPage",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         description="Page Number",
     *         format="int64",
     *         in="query",
     *         name="currentPage",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         description="Column Name",
     *         in="query",
     *         name="orderByName",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="ASC Or DESC",
     *         in="query",
     *         name="orderBy",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Event ID",
     *         in="query",
     *         name="event_id",
     *         required=false,
     *         format="int64",
     *         type="integer"
     *     ),
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/ImageList")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */


    public function list()
    {
        if (is_numeric($this->perPage) && (empty($this->currentPage) || !is_numeric($this->currentPage)))
        {
            return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => 'Bad Input']);
        }
        if ($this->perPage == 'false')
        {
            $this->currentPage = intval(0);
        }
            
        $this->perPage = !is_numeric($this->perPage) ? $this->perPage == 'false' ? intval($this->perPage) : 'error':intval($this->perPage);
    
        $this->currentPage = empty($this->currentPage) ? intval(0) : $this->currentPage;
        $this->currentPage = !is_numeric($this->currentPage) ? $this->currentPage == 'false' ? intval($this->currentPage) : 'error':intval($this->currentPage);

        $this->orderByName = empty($this->orderByName) ? 'image_id':$this->orderByName;

        if($this->orderBy == 'DESC' || $this->orderBy == 'ASC' || $this->orderBy == '')
        {
            $this->orderBy = $this->orderBy == 'DESC' ? 'DESC':'ASC'; 
        }
        else
        {
            return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => 'Bad Input']);
        }

        if(is_integer($this->perPage) && is_integer($this->currentPage))
        {
            try
            {
                $result = parent::list();
                return $this->sendResponse(200, $result);
            }
            catch(Exception $e)
            {
                return $this->sendResponse(500, ['status' => 'ERROR', 'messages' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
        }
        else
        {
            return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => 'Bad Input']);
        }
    }
    /**
     * @SWG\Delete(
     *     tags={"Image"},
     *     path="/image/{param}",
     *     description="Deletes company by ID",
     *     summary="Delete company",
     *     operationId="DeleteCompanyById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="Image ID",
     *         in="path",
     *         name="param",
     *         required=true,
     *         type="integer"
     *     ),
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=204,
     *         description="Deleted",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not Found",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */
    public function delete($param)
    {
        $requestForDelete = array('image_id' => $param);
        $expectedForDelete = array('image_id' => 'required|numeric');
        
        if ($this->isTokenValid())
        {
            $errors = $this->validation($requestForDelete, $expectedForDelete);

            try
            {
                if($errors->isEmpty())
                {
                    $image = \App\Image::where('image_id', $param)->first();
                    if($image == null)
                    {
                        return $this->sendResponse(404, ['status' => 'ERROR', 'messages' => 'No such Record exists']);       
                    }
                    $result = parent::delete($param);
                }
                else
                {
                    return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => implode("'\n'", $errors->all())]);
                }
                if ($result == 1) 
                {
                    unlink('../public/'.$image['url']);
                    return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Record Deleted Successfully']);
                }
                else
                {
                    return $this->sendResponse(404, ['status' => 'ERROR', 'messages' => 'No such Record exists']);
                }
            }
            catch(Exception $e)
            {
                return $this->sendResponse(500, ['status' => 'ERROR', 'messages' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
        }
        else
        {
            return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Unauthorized']);
        }
    }

    
    /**
     * @SWG\Get(
     *     tags={"Image"},
     *     path="/image/{id}",
     *     description="Returns image by id",
     *     summary="Get image",
     *     operationId="GetCompanyById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of image to fetch",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/Image")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */
    public function get($id)
    {
        $requestForGet = ['company_id' => $id];
        $expectedForGet = ['company_id' => 'required|numeric'];
        if ($this->isTokenValid())
        {
            $errors = $this->validation($requestForGet, $expectedForGet);
            if($errors->isEmpty())
            {
                try
                {
                    $result = parent::get($id);
                    if ($result == NULL)
                    {
                        return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => 'No Such Record Exists']);
                    }
                    return $this->sendResponse(200,$result);
                }
                catch(Exception $e)
                {
                    return $this->sendResponse(500, ['status' => 'ERROR', 'messages' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                }
            }
            else
            {
                return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => implode("'\n'", $errors->all())]);
            }
        }
        else
        {
            return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Unauthorized']);
        }
    }

 // Option method
    public function option()
    {    
        return $this->sendResponse();   
    }
}