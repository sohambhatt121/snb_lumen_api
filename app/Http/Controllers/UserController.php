<?php
/**
 *
 * @SWG\Definition(definition="Error", type="object", required={"messages"},
 *         @SWG\Property(property="status", type="string"),
 *         @SWG\Property(property="messages", type="array", @SWG\Items(type="string"))
 * )
 */


/**
 *@SWG\Definition(type="object", definition="UserList",
 *   @SWG\Property(
 *     property="items",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/User")
 *   )
 *)
 */

 /**
  * @SWG\Definition(definition="UserUpdate", type="object",
  *     @SWG\Property(property="user_firstname", type="string"),
  *     @SWG\Property(property="user_lastname", type="string"),
  *     @SWG\Property(property="user_email", type="string"),
  *     @SWG\Property(property="user_contact", type="string"),
  *     @SWG\Property(property="user_region", type="string"),
  *     @SWG\Property(property="user_country", type="string"),
  *     @SWG\Property(property="user_state", type="string"),
  *     @SWG\Property(property="user_company", type="string"),
  *     @SWG\Property(property="user_project", type="string"),
  *     @SWG\Property(property="project_details", type="string"),
  *     @SWG\Property(property="time_estimation", type="string"),
  *     @SWG\Property(property="budget_estimation", type="string"),
  *     @SWG\Property(property="industry", type="string"),
  *     @SWG\Property(property="subscribe", type="number" , format="int64"),
  * )
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Laracasts\Validation\FormValidator;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    public $currentPage;
    public $perPage;
    public $orderByName;
    public $orderBy;
    public $condition = [];

    public function getModelName()
    {
        return 'User';
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
     *     tags={"Users"},
     *     path="/user",
     *     description="Creates user",
     *     summary="Create user",
     *     operationId="CreateUser",
     *     @SWG\Parameter(
     *       in="body",
     *       name="body",
     *       description="Insert user's Information",
     *       required=false,
     *       @SWG\Schema(ref="#/definitions/User")
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
     *         @SWG\Schema(ref="#/definitions/User")
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
            'user_id' => 'numeric',
            'user_firstname' => 'required',
            'user_lastname' => 'required',
            'user_email' => 'required|email',
            'user_contact' => 'nullable',
            'user_region' => 'alpha|required',
            'user_country' => 'nullable',
            'user_state' => 'nullable',
            'user_company' => 'nullable',
            'user_project' => 'nullable',
            'project_details' => 'nullable',
            'time_estimation' => 'nullable',
            'budget_estimation' => 'nullable',
            'industry' => 'nullable',
            'subscribe' => 'numeric|in:0,1||nullable'
        ];

        $errors = $this->validation($this->requestData,$expectedForCreate);

        if($errors->isEmpty())
        {
            try
            {
                $result = parent::create();
                return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Record Created Successfully', 'id' => $result['id']]); 
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
     *     tags={"Users"},
     *     path="/user/{id}",
     *     description="Update user",
     *     summary="Update user",
     *     operationId="UpdateUser",
     *	   @SWG\Parameter(
     *         description="ID of user to update",
     *         format="int64",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *       in="body",
     *       name="body",
     *       description="Update User's Information",
     *       required=false,
     *       @SWG\Schema(ref="#/definitions/UserUpdate")
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
     *         @SWG\Schema(ref="#/definitions/User")
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
            'user_firstname' => 'nullable',
            'user_lastname' => 'nullable',
            'user_email' => 'nullable|email',
            'user_contact' => 'nullable',
            'user_region' => 'nullable',
            'user_country' => 'nullable',
            'user_state' => 'nullable',
            'user_company' => 'nullable',
            'user_project' => 'nullable',
            'project_details' => 'nullable',
            'time_estimation' => 'nullable',
            'budget_estimation' => 'nullable',
            'industry' => 'nullable',
            'subscribe' => 'numeric|in:0,1|nullable'
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
     *     tags={"Users"},
     *     path="/user",
     *     description="Returns list of users",
     *     summary="Get users",
     *     operationId="GetUsers",
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
     *         name="user_email",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Region",
     *         in="query",
     *         name="user_region",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Country",
     *         in="query",
     *         name="user_country",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="State",
     *         in="query",
     *         name="user_state",
     *         required=false,
     *         type="string"
     *     ),
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/UserList")
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

        $this->orderByName = empty($this->orderByName) ? 'user_id':$this->orderByName;
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
            if ($this->isTokenValid() && $this->isAdminUser())
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
     *     tags={"Users"},
     *     path="/user/{id}",
     *     description="Deletes user by id",
     *     summary="Delete user",
     *     operationId="DeleteUserById",
     *     @SWG\Parameter(
     *         description="ID of user to fetch",
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
        return $this->sendResponse(501, ['status' => 'ERROR', 'messages' => 'Method not implemented']);

        $requestForDelete = array('user_id' => $id);
        $expectedForDelete = array('user_id' => 'required|numeric');
        
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
     *     tags={"Users"},
     *     path="/user/{id}",
     *     description="Returns user by id",
     *     summary="Get user",
     *     operationId="GetUserById",
     *     @SWG\Parameter(
     *         description="ID of user to fetch",
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
     *         description="user response",
     *         @SWG\Schema(ref="#/definitions/User")
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
        $requestForGet = ['user_id' => $id];
        $expectedForGet = ['user_id' => 'required|numeric'];

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