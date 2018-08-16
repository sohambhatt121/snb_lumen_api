<?php

/**
 *@SWG\Definition(type="object", definition="ProgramList",
 *   @SWG\Property(
 *     property="items",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/Program")
 *   )
 *)
 */

/**
  * @SWG\Definition(definition="ProgramSave", type="object",
  *     @SWG\Property(property="program_title", type="string"),
  *     @SWG\Property(property="program_description", type="string"),
  *     @SWG\Property(property="program_time", type="string"),
  *     @SWG\Property(property="program_date", type="string"),
  *     @SWG\Property(property="program_location", type="string"),
  *     @SWG\Property(property="program_address", type="string"),
  *     @SWG\Property(property="contact_person", type="string"),
  *     @SWG\Property(property="contact_number", type="string")
  * )
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Laracasts\Validation\FormValidator;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use DateTime;

class ProgramController extends Controller
{
    public $currentPage;
    public $perPage;
    public $orderByName;
    public $orderBy;
    public $condition = [];

    public function getModelName()
    {
        return 'Program';
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
     *     tags={"Program"},
     *     path="/program",
     *     description="Store program",
     *     summary="Store program",
     *     operationId="StoreProgram",
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
     *       @SWG\Schema(ref="#/definitions/ProgramSave")
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
     *         @SWG\Schema(ref="#/definitions/ProgramSave")
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
            'program_title' => 'required',
            'program_description' => 'required',
            'program_time' => 'required',
            'program_date' => 'required',
            'program_location' => 'required',
            'program_address' => 'required',
            'contact_person' => 'required',
            'contact_number' => 'required'
        ];
        //var_dump($this->requestData);exit();
        $errors = $this->validation($this->requestData,$expectedForCreate);

        if($errors->isEmpty())
        {
            try
            {
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
     *     tags={"Program"},
     *     path="/program/{id}",
     *     description="Update program",
     *     summary="Update program",
     *     operationId="UpdateProgram",
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
     *       @SWG\Schema(ref="#/definitions/ProgramSave")
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
     *         @SWG\Schema(ref="#/definitions/Program")
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
            'program_title' => 'nullable',
            'program_description' => 'nullable',
            'program_time' => 'nullable',
            'program_date' => 'nullable',
            'program_location' => 'nullable',
            'program_address' => 'nullable',
            'contact_person' => 'nullable',
            'contact_number' => 'nullable'
        ];

        $errors = $this->validation($this->requestData, $expectedForUpdate);

        if($errors->isEmpty())
        {
            try
            {
                $admin_id = $this->isTokenValid();
                if($admin_id)
                {
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
     *     tags={"Program"},
     *     path="/program",
     *     description="Returns list of program",
     *     summary="Get program",
     *     operationId="GetProgram",
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
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/ProgramList")
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

        $this->orderByName = empty($this->orderByName) ? 'program_id':$this->orderByName;
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
     *     tags={"Program"},
     *     path="/program/{id}",
     *     description="Deletes Program by id",
     *     summary="Delete Program",
     *     operationId="DeleteProgramById",
     *     @SWG\Parameter(
     *          description="Authorization token",
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *         description="ID of Program to delete",
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
     *     tags={"Program"},
     *     path="/program/{id}",
     *     description="Returns program by id",
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
     *         description="ID of Program to fetch",
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
     *         @SWG\Schema(ref="#/definitions/Program")
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
                else
                {
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