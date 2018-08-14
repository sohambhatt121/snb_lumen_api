<?php

/**
 *@SWG\Definition(type="object", definition="AdminList",
 *   @SWG\Property(
 *     property="items",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/Admin")
 *   )
 *)
 */


 /**
  * @SWG\Definition(definition="AdminUpdate", type="object",
  *     @SWG\Property(property="admin_email", type="string"),
  *     @SWG\Property(property="admin_firstname", type="string"),
  *     @SWG\Property(property="admin_lastname", type="string"),
  *     @SWG\Property(property="admin_contact", type="string" ),
  *     @SWG\Property(property="admin_user_name", type="string"),
  *     @SWG\Property(property="admin_password", type="string", format="password"),
  *     @SWG\Property(property="admin_block", type="number" , format="int64")
  * )
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Laracasts\Validation\FormValidator;
use Illuminate\Http\Request;
use Exception;
use DateTime;
use Illuminate\Database\QueryException;

class AdminController extends Controller
{
    public $currentPage;
    public $perPage;
    public $orderByName;
    public $orderBy;
    public $condition = [];

    public function getModelName()
    {
        return 'Admin';
    }

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->perPage = $request->query('perPage');
        $this->currentPage = $request->query('currentPage');
        $this->orderByName = $request->query('orderByName');
        $this->orderBy = strtoupper($request->query('orderBy'));
      
        if(isset($this->requestData['admin_password']))
        {
            $this->requestData['admin_password'] = Hash::make($this->requestData['admin_password']);
        }

        $this->condition = $request->query();
        unset($this->condition['perPage'],$this->condition['currentPage'],$this->condition['orderByName'],$this->condition['orderBy']);
    }

    /**
     * @SWG\Post(
     *     tags={"Administrator"},
     *     path="/administrator",
     *     description="Creates admin",
     *     summary="Create admin",
     *     operationId="CreateAdmin",
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
     *       description="Insert admin's Information",
     *       required=false,
     *       @SWG\Schema(ref="#/definitions/Admin")
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
     *         @SWG\Schema(ref="#/definitions/Admin")
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
            'admin_id' => 'numeric',
            'admin_firstname' => 'required',
            'admin_lastname' => 'required',
            'admin_email' => 'email',
            'admin_contact' => 'regex:/[0-9]{10}/|required',
            'admin_user_name' => 'required',
            'admin_password' => 'min:4|required',
            'admin_type' => 'required|numeric|in:1,2'
        ];

        $errors = $this->validation($this->requestData,$expectedForCreate);

        if($errors->isEmpty())
        {
            try
            {
                $admin_id = $this->isSuperAdminUser();
                if($admin_id)
                {
                    $this->requestData['added_by'] = $admin_id;

                    $check_un = \App\Admin::where('admin_user_name', $this->requestData['admin_user_name'])->first();
                    //var_dump($check_un);exit();
                    if($check_un == null)
                    {
                        $result = parent::create();
                        return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Record Created Successfully', 'id' => $result['id']]);    
                    }
                    else
                    {
                        return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Username is already taken']);
                    }    
                }
                else
                {
                    return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Unauthorized']);
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

	/**
     * @SWG\Put(
     *     tags={"Administrator"},
     *     path="/administrator/{id}",
     *     description="Update admin",
     *     summary="Update admin",
     *     operationId="UpdateAdmin",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *	   @SWG\Parameter(
     *         description="ID of admin to update",
     *         format="int64",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *       in="body",
     *       name="body",
     *       description="Update admin's Information",
     *       required=false,
     *       @SWG\Schema(ref="#/definitions/AdminUpdate")
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
     *         @SWG\Schema(ref="#/definitions/Admin")
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
            'admin_email' => 'email|nullable',
            'admin_firstname' => 'nullable',
            'admin_lastname' => 'nullable',
            'admin_contact' => 'regex:/[0-9]{10}/|nullable',
            'admin_password' => 'min:4|nullable',
            'admin_user_name' => 'min:4|nullable',
            'admin_block' => 'numeric|in:0,1'
        ];
        
        $errors = $this->validation($this->requestData, $expectedForUpdate);
        if($errors->isEmpty())
        {
            try
            {
                $users = \App\Admin::where('admin_id',$id)->first();

                if($users != NULL && $this->isTokenValid())
                {
                    if(isset($this->requestData['admin_block']) && $this->requestData['admin_block'] == 1)
                    {
                        $admin_id = $this->isSuperAdminUser();
                        if(!isset($admin_id))
                        {
                            return $this->sendResponse(404, ['status' => 'ERROR', 'messages' => 'Invalid Access']);
                        }
                        else
                        {
                            $this->requestData['blocked_by'] = $admin_id;
                            $this->requestData['blocked_date'] = (new DateTime())->format('Y-m-d H:m:s');
                        }
                    }

                    if(isset($this->requestData['admin_user_name']))
                    {
                        $check_un = \App\Admin::where('admin_user_name', $this->requestData['admin_user_name'])->first();
                        //var_dump($check_un);exit();
                        if($check_un != null)
                        {
                            return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Username is already taken']);
                        }
                    }

                    $result = parent::update($id);
                    return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Record Updated Successfully']);
                }
                else
                {
                    return $this->sendResponse(404, ['status' => 'ERROR', 'messages' => 'User Not found or Invalid Access']);
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
     *     tags={"Administrator"},
     *     path="/administrator",
     *     description="Returns list of admin",
     *     summary="Get admin",
     *     operationId="GetAdmin",
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
     *         name="admin_email",
     *         required=false,
     *         type="string"
     *     ),
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/AdminList")
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

        $this->orderByName = empty($this->orderByName) ? 'admin_id':$this->orderByName;
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
     *     tags={"Administrator"},
     *     path="/administrator/{id}",
     *     description="Deletes admin by id",
     *     summary="Delete admin",
     *     operationId="DeleteAdminById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of admin to fetch",
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

        $requestForDelete = array('admin_id' => $id);
        $expectedForDelete = array('admin_id' => 'required|numeric');
        
        if ($this->isTokenValid() && $this->isAdminUser())
        {
            $errors = $this->validation($requestForDelete, $expectedForDelete);
            if($errors->isEmpty())
            {
                try
                {
                    $result=parent::delete($id);
                    if ($result == 1) 
                    {
                        return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Delete Record Successfully']);
                    }
                    else
                    {
                        return $this->sendResponse(404, ['status' => 'ERROR', 'messages' => 'No such Record exist']);    
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
        else
        {
            return $this->sendResponse(401, ['status' => 'ERROR', 'messages' => 'Unauthorized']);
        }
    }

    /**
     * @SWG\Get(
     *     tags={"Administrator"},
     *     path="/administrator/{id}",
     *     description="Returns admin by id",
     *     summary="Get admin",
     *     operationId="GetAdminById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of admin to fetch",
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
     *         description="admin response",
     *         @SWG\Schema(ref="#/definitions/Admin")
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
        $requestForGet = ['admin_id' => $id];
        $expectedForGet = ['admin_id' => 'required|numeric'];

        if ($this->isTokenValid())
        {
            $errors = $this->validation($requestForGet, $expectedForGet);
            if($errors->isEmpty())
            {
                try
                {
                    $result = parent::get($id);
                    if($result != null)
                    {
                        return $this->sendResponse(200,$result);
                    }
                    else
                    {
                        return $this->sendResponse(404, ['status' => 'ERROR', 'messages' => 'No such Record exist']);
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
?>