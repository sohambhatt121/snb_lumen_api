<?php

/**
 *@SWG\Definition(type="object", definition="EventList",
 *   @SWG\Property(
 *     property="items",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/Event")
 *   )
 *)
 */

/**
  * @SWG\Definition(definition="EventSave", type="object",
  *     @SWG\Property(property="title", type="string"),
  *     @SWG\Property(property="description", type="string"),
  *     @SWG\Property(property="detail", type="string"),
  *     @SWG\Property(property="event_date", type="string"),
  *     @SWG\Property(property="month", type="string"),
  *     @SWG\Property(property="year", type="string"),
  *     @SWG\Property(property="video_1", type="string"),
  *     @SWG\Property(property="video_2", type="string")
  * )
 */

 /**
  * @SWG\Definition(definition="EventUpdate", type="object",
  *     @SWG\Property(property="title", type="string"),
  *     @SWG\Property(property="description", type="string"),
  *     @SWG\Property(property="detail", type="string"),
  *     @SWG\Property(property="event_date", type="string"),
  *     @SWG\Property(property="month", type="string"),
  *     @SWG\Property(property="year", type="string"),
  *     @SWG\Property(property="video_1", type="string"),
  *     @SWG\Property(property="video_2", type="string"),
  *     @SWG\Property(property="stared", type="number" , format="int64"),
  *     @SWG\Property(property="deleted", type="number" , format="int64")

  * )
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Laracasts\Validation\FormValidator;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use DateTime;

class EventController extends Controller
{
    public $currentPage;
    public $perPage;
    public $orderByName;
    public $orderBy;
    public $condition = [];

    public function getModelName()
    {
        return 'Event';
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

        $this->req = $request;
    }

    /**
     * @SWG\Post(
     *     tags={"Event"},
     *     path="/event",
     *     description="Store event",
     *     summary="Store event",
     *     operationId="StoreEvent",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *       in="body",
     *       name="body",
     *       description="Store Event",
     *       required=false,
     *       @SWG\Schema(ref="#/definitions/EventSave")
     *     ),
     *     consumes={
     *         "application/json"
     *     },
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/EventSave")
     *     ),
     *   @SWG\Response(
     *     response="400",
     *     description="Bad Request",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not Found",
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
            'title' => 'required',
            'description' => 'required',
            'detail' => 'required',
            'event_date' => 'required',
            'video_1' => 'required',
            'video_2' => 'required',
            'month' => 'required',
            'year' => 'required'
        ];
        //var_dump($this->requestData);exit();
        $errors = $this->validation($this->requestData,$expectedForCreate);

        if($errors->isEmpty())
        {
            try
            {
                // $this->requestData['event_date'] = date_create($this->requestData['event_date']);
                //var_dump($this->requestData['event_date']);exit();
                $added_by = $this->isTokenValid();
                if($added_by)
                {
                    $this->requestData['added_by'] = $added_by;
                    $this->requestData['updated_by'] = $added_by;
                }
                else
                {
                    return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Unauthorized']);
                }

                $result = parent::create();
                return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Data Stored Successfully']); 
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

    /**
     * @SWG\Put(
     *     tags={"Event"},
     *     path="/event/{id}",
     *     description="Update event",
     *     summary="Update event",
     *     operationId="UpdateEvent",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of event to update",
     *         format="int64",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *       in="body",
     *       name="body",
     *       description="Update event",
     *       required=false,
     *       @SWG\Schema(ref="#/definitions/EventUpdate")
     *     ),
     *     consumes={
     *         "application/json"
     *     },
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/Event")
     *     ),
     *   @SWG\Response(
     *     response="400",
     *     description="Bad Request",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response="401",
     *     description="Unauthorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not Found",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */

    public function update($id)
    {
        $expectedForUpdate = [
            'title' => 'nullable',
            'description' => 'nullable',
            'detail' => 'nullable',
            'event_date' => 'nullable',
            'video_1' => 'nullable',
            'video_2' => 'nullable',
            'month' => 'nullable',
            'year' => 'nullable',
            'stared' => 'numeric|in:0,1|nullable',
            'deleted' => 'numeric|in:0,1|nullable'
        ];

        $errors = $this->validation($this->requestData, $expectedForUpdate);

        if($errors->isEmpty())
        {
            try
            {
                $admin_id = $this->isTokenValid();
                if($admin_id)
                {
                    if(isset($this->requestData['deleted']) && $this->requestData['deleted'] == 1)
                    {
                        $getInfo = \App\Token::where('auth_token', $this->headerToken)->first();
                        $this->requestData['deleted_by'] = $admin_id;
                        $this->requestData['deleted_date'] = (new DateTime())->format('Y-m-d H:m:s');
                    }
                    $this->requestData['updated_by'] = $admin_id;
                    $result = parent::update($id);
                    return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Record Updated Successfully']);    
                }
                else
                {
                    return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Unauthorized']);
                }
                
            }
            catch(Exception $e)
            {
                return $this->sendResponse(500,['status'=>'ERROR', 'messages'=>$e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
        }
        else
        {   
            return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => implode("'\n'", $errors->all())]);
        }
    }

    /**
     * @SWG\Get(
     *     tags={"Event"},
     *     path="/event",
     *     description="Returns list of event",
     *     summary="Get event",
     *     operationId="GetEvent",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
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
     *         description="Stared",
     *         in="query",
     *         name="stared",
     *         required=false,
     *         format="int64",
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         description="Deleted",
     *         in="query",
     *         name="deleted",
     *         required=false,
     *         format="int64",
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         description="Month",
     *         in="query",
     *         name="month",
     *         required=false,
     *         format="int64",
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         description="Year",
     *         in="query",
     *         name="year",
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
     *         @SWG\Schema(ref="#/definitions/EventList")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Input",
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

        $this->orderByName = empty($this->orderByName) ? 'event_id':$this->orderByName;
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
            if ($this->isTokenValid())
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
                return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Unauthorized']);
            }
        }
        else
        {    
            return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => 'Bad Input']);
        }
    }

    /**
     * @SWG\Delete(
     *     tags={"Event"},
     *     path="/event/{id}",
     *     description="Deletes Message by id",
     *     summary="Delete event",
     *     operationId="DeleteEventById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of event to delete",
     *         format="int64",
     *         in="path",
     *         name="id",
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
     *         response="403",
     *         description="Not Authorized Invalid or missing Authorization header",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */
    
    
    public function delete($id)
    {
        //return $this->sendResponse(501, ['status' => 'ERROR', 'messages' => 'Method not implemented']);

        $requestForDelete = array('id' => $id);
        $expectedForDelete = array('id' => 'required|numeric');
        
        $errors = $this->validation($requestForDelete, $expectedForDelete);
        if($errors->isEmpty())
        {
            try
            {
                $result = parent::delete($id);
                return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Delete Record Successfully']);
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

    /**
     * @SWG\Get(
     *     tags={"Event"},
     *     path="/event/{id}",
     *     description="Returns event by id",
     *     summary="Get event",
     *     operationId="GetEventById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of event to fetch",
     *         format="int64",
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
     *         description="message response",
     *         @SWG\Schema(ref="#/definitions/Event")
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Not Authorized Invalid or missing Authorization header",
     *         @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not Found",
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
        $requestForGet = ['id' => $id];
        $expectedForGet = ['id' => 'required|numeric'];

        $errors = $this->validation($requestForGet, $expectedForGet);
        if($errors->isEmpty())
        {
            try
            {
                $result = parent::get($id);
                /*$this->condition['event_id'] = $id;
                $result = $this->model::leftjoin('images','event_id','=',$id)->where($this->condition)->first();
                var_dump($result);exit();*/
                if($result == NULL)
                {
                    return $this->sendResponse(404, ['status' => 'ERROR', 'messages' => 'No such Record exist']);
                }
                else
                {
                    /*$image = \App\Image::where('event_id', $id);
                    $count = $image->count();
                    $image = (array)$image;
                    //var_dump($image->getAttributes());exit();
                    for ($i=0; $i < $count ; $i++) { 
                        var_dump($image[$i]);
                    }
                    exit();*/
                    return $this->sendResponse(200,$result);
                }
                
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

    // Option method
    public function option()
    {    
        return $this->sendResponse();   
    }
}
?>