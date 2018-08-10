<?php



/**
  * @SWG\Definition(definition="AuthToken", type="object",
  *     @SWG\Property(property="status", type="string"),
  *     @SWG\Property(property="Token", type="string")
  * )
 */



 /**
  * @SWG\Definition(definition="AdminInfo", type="object",
  *     @SWG\Property(property="token_id", type="integer", format="int64"),
  *     @SWG\Property(property="admin_email", type="integer", format="int64"),
  *     @SWG\Property(property="created_at", type="string", format="date-time"),
  *     @SWG\Property(property="updated_at", type="string", format="date-time"),
  *     @SWG\Property(property="admin_name", type="string"),
  *     @SWG\Property(property="admin_contact", type="integer", format="int64"),
  *     @SWG\Property(property="admin_type", type="string"),
  *     @SWG\Property(property="admin_password", type="string", format="password")
  * )
 */



/**
 * @SWG\Definition(definition="LoginInfo", type="object",
 *      required={"username", "password"},
 *      @SWG\Property(property="admin_user_name", type="string"),
 *      @SWG\Property(property="admin_password", type="string")
 * )
 */



namespace App\Http\Controllers; 
use App;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Exception;

class AuthTokenController extends Controller
{
    public $result;

    public function getModelName()
    {
        return 'Token';
    }

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

/**
    * @SWG\Post(path="/authtoken",
    *   tags={"Auth Token"},
    *   description="Generate token",
    *   summary="Create token",
    *   operationId="createAuthtoken",
    *    consumes={
    *         "application/json"
    *     },
    *     produces={
    *         "application/json"
    *     },
    *   @SWG\Parameter(
    *     description="Generate Token",
    *     name="body",
    *     in="body",
    *     @SWG\Schema(ref="#/definitions/LoginInfo"),
    *     required=true
    *   ),
    *   @SWG\Response(
    *     response="200",
    *     description="Success",
    *     @SWG\Schema(ref="#/definitions/AuthToken")
    *   ),
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
    *   @SWG\Response(
    *     response=500,
    *     description="Internal Server Error",
    *     @SWG\Schema(ref="#/definitions/ErrorResponse")
    *   )
    * )
    */


    public function create()
    {   
        $expectedForCreate = [
            'admin_user_name' => 'required|min:4',
            'admin_password' => 'required|min:4'
        ];

        $errors = $this->validation($this->requestData, $expectedForCreate);

        try
        {
            if($errors->isEmpty())
            {
                $user_name = $this->requestData['admin_user_name'];
                $password = $this->requestData['admin_password'];
                $this->result = $this->verifyUser($user_name,$password);
                if($this->result == false)
                {
                    return $this->sendResponse(401,['status'=>'ERROR', 'messages'=>'Unauthorized']);
                }
                else
                {
                    $token = str_random(30).$user_name;
                    $this->requestData = array('user_name'=>$user_name,'admin_id'=>$this->result, 'auth_token'=> $token );
                    $response = parent::create();
                    return $this->sendResponse(200, ['status'=>'SUCCESS', 'Token'=>$token]);
                }
            }
            else
            {
                return $this->sendResponse(400, ['status' => 'ERROR', 'messages' => implode("'\n'", $errors->all())]);
            }
        }
        catch(Exception $e)
        {
            return $this->sendResponse(500,['status'=>'ERROR', 'messages'=>$e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    private function verifyUser($user_name,$password)
    {   
        try
        {
            $user = \App\Admin::where('admin_user_name',$user_name)->first();
            if($user == NULL)
            {
                return false;
            }
            else
            {
                $user = $user->getAttributes(); 
                return Hash::check($password,$user['admin_password'])?$user['admin_id']:false;
            }
        }
        catch(\Exception $e)
        {
            return $e;
        }
    }

    /*private function createRandomToken($user_name)
    {
        $token = str_random(20).$user_name;
        return $token;
    }*/




/**
    * @SWG\Get(path="/authtoken/{token}",
    *   tags={"Auth Token"},
    *   description="Get admin by token",
    *   summary="Get token",
    *   operationId="getAdminByToken",
    *    consumes={
    *         "application/json"
    *     },
    *     produces={
    *         "application/json"
    *     },
    *   @SWG\Parameter(
    *     description="Authorization Token",
    *     type="string",
    *     name="token",
    *     in="path",
    *     required=true
    *   ),
    *   @SWG\Response(
    *     response="200",
    *     description="Success",
    *     @SWG\Schema(ref="#/definitions/AdminInfo")
    *   ),
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
    *   @SWG\Response(
    *     response=500,
    *     description="Internal Server Error",
    *     @SWG\Schema(ref="#/definitions/ErrorResponse")
    *   )
    * )
    */


    public function get($token)
    {
        $requestForGet = ['token' => $token];
        $expectedForGet = ['token' => 'required|alpha_num'];
        $errors = $this->validation($requestForGet, $expectedForGet);
        if($errors->isEmpty())
        {
            try
            {
                $getInfo = $this->model::where('auth_token', $token)->first();
                if($getInfo != NULL && $getInfo != NULL)
                {
                    //$userInfo = $getInfo->admin->getAttributes();
                    return $this->sendResponse(200, ['status'=>'SUCCESS', 'Information'=>$getInfo]);
                }
                else
                {
                    return $this->sendResponse(401,['status'=>'ERROR', 'messages'=>'Unauthorized']);
                }
                
            }
            catch(\Exception $e)
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
     * @SWG\Delete(
     *     tags={"Auth Token"},
     *     path="/authtoken/{token}",
     *     description="Deletes token",
     *     summary="Delete token",
     *     operationId="DeleteToken",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="fetch token",
     *         in="path",
     *         name="token",
     *         required=true,
     *         type="string"
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
    
    
    public function delete($token)
    {
        $requestForDelete = array('token' => $token);
        $expectedForDelete = array('token' => 'required');
        
        $errors = $this->validation($requestForDelete, $expectedForDelete);
        if($errors->isEmpty() && $this->headerToken === $token)
        {
            try
            {
                $getInfo = $this->model::where('auth_token', $token)->first();
                parent::delete($getInfo['token_id']);
                return $this->sendResponse(200,['status' => 'SUCCESS', 'messages' => 'Record Deleted Successfully']);
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

    public function option()
    {    
        return $this->sendResponse();   
    }
}
?>
