<?php
/**
 *
 * @SWG\Definition(definition="Error", type="object", required={"messages"},
 *         @SWG\Property(property="status", type="string"),
 *         @SWG\Property(property="messages", type="array", @SWG\Items(type="string"))
 * )
 */


/**
 *@SWG\Definition(type="object", definition="MessageList",
 *   @SWG\Property(
 *     property="items",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/Message")
 *   )
 *)
 */

/**
  * @SWG\Definition(definition="MessageSave", type="object",
  *     @SWG\Property(property="first_name", type="string"),
  *     @SWG\Property(property="last_name", type="string"),
  *     @SWG\Property(property="email", type="string"),
  *     @SWG\Property(property="phone", type="string"),
  *     @SWG\Property(property="message", type="string")
  * )
 */

 /**
  * @SWG\Definition(definition="MessageUpdate", type="object",
  *     @SWG\Property(property="first_name", type="string"),
  *     @SWG\Property(property="last_name", type="string"),
  *     @SWG\Property(property="email", type="string"),
  *     @SWG\Property(property="phone", type="string"),
  *     @SWG\Property(property="message", type="string"),
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

class MessageController extends Controller
{
    public $currentPage;
    public $perPage;
    public $orderByName;
    public $orderBy;
    public $condition = [];

    public function getModelName()
    {
        return 'Message';
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
     *     tags={"Message"},
     *     path="/message",
     *     description="Store message",
     *     summary="Store message",
     *     operationId="StoreMessage",
     *     @SWG\Parameter(
     *       in="body",
     *       name="body",
     *       description="Store Message",
     *       required=false,
     *       @SWG\Schema(ref="#/definitions/MessageSave")
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
     *         @SWG\Schema(ref="#/definitions/MessageSave")
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'required|regex:/[+][0-9]{10,15}/',
            'message' => 'required'
        ];
        
        $errors = $this->validation($this->requestData,$expectedForCreate);

        if($errors->isEmpty())
        {
            try
            {
                $this->requestData['ip'] = $this->req->ip();
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
     *     tags={"Message"},
     *     path="/message/{id}",
     *     description="Update message",
     *     summary="Update message",
     *     operationId="UpdateMessage",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *	   @SWG\Parameter(
     *         description="ID of message to update",
     *         format="int64",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *       in="body",
     *       name="body",
     *       description="Update Message",
     *       required=false,
     *       @SWG\Schema(ref="#/definitions/MessageUpdate")
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
     *         @SWG\Schema(ref="#/definitions/Message")
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
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable|regex:/[+][0-9]{10,15}/',
            'message' => 'nullable',
            'stared' => 'numeric|in:0,1|nullable',
            'deleted' => 'numeric|in:0,1|nullable'
        ];

        $errors = $this->validation($this->requestData, $expectedForUpdate);

        if($errors->isEmpty())
        {
            try
            {
                $result = parent::update($id);
                return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Record Updated Successfully']);
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
     *     tags={"Message"},
     *     path="/message",
     *     description="Returns list of messages",
     *     summary="Get messages",
     *     operationId="GetMessages",
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
     *         description="search by email",
     *         format="email",
     *         in="query",
     *         name="email",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="search by phone",
     *         in="query",
     *         name="phone",
     *         required=false,
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
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/MessageList")
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

        $this->orderByName = empty($this->orderByName) ? 'message_id':$this->orderByName;
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
     *     tags={"Message"},
     *     path="/message/{id}",
     *     description="Deletes Message by id",
     *     summary="Delete message",
     *     operationId="DeleteMessageById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of message to delete",
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
     *     tags={"Message"},
     *     path="/message/{id}",
     *     description="Returns message by id",
     *     summary="Get message",
     *     operationId="GetMessageById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of message to fetch",
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
     *         @SWG\Schema(ref="#/definitions/Message")
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
                if($result == NULL)
                {
                    return $this->sendResponse(404, ['status' => 'ERROR', 'messages' => 'No such Record exist']);
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

    // Option method
    public function option()
    {    
        return $this->sendResponse();   
    }
}
?>