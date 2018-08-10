<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App;
use Exception;
use Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Database\QueryException;

class Controller extends BaseController
{
    protected $model;
    protected $modelName;
    public $requestData;
    public $headerToken;
    public $requestFile;
    public $queryString;

    public function __construct(Request $request)
    {
        $this->modelName = 'App\\' . $this->getModelName();
        $this->model = new $this->modelName();
        $this->requestData = json_decode($request->getContent(), TRUE);
        $this->headerToken = $request->header('Authorization');
        $this->queryString = $request->query();
        $this->headerType = $request->header('Content-Type');
        
        if($this->headerType == 'application/json')
        {
            $this->requestData = json_decode($request->getContent(), TRUE);
        }
        else
        {
            $this->requestFile = $request->file('company_logo');
            $this->requestData = $request->post();
        }
    }

    public function isTokenValid()
    {
    	//return true;
        $getInfo = \App\Token::where('auth_token', $this->headerToken)->first();
        if($getInfo == NULL)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function resetPasswordTokenValid()
    {
        $getinfo = $this->model::where('reset_token', $this->requestData['reset_token'])->first();
        if($getinfo['admin_id'] == NULL)
        {
            return false;
        }
        else
        {
            return $getinfo;
        }
    }

    /*public function isAdminUser()
    {
    	//return true;
        $getInfo = \App\Token::where('auth_token', $this->headerToken)->first();
        if($getInfo != NULL && $getInfo->adminid != NULL)
        {
            $user = $getInfo->adminid->getAttributes();
            if($user['admin_type'] == 'admin')
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }*/

    /*public function isSalesPerson()
    {
        $getInfo = \App\Token::where('auth_token', $this->headerToken)->first();
        if($getInfo != NULL && $getInfo->admin != NULL)
        {
            $user = $getInfo->admin->getAttributes();
            if($user['admin_type'] == 'salesperson')
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }*/

    public function list()
    {   
        return $this->model::orderBy($this->orderByName,$this->orderBy)->where($this->condition)->paginate($this->perPage, ['*'], 'page',$this->currentPage);
    }
    
    public function get($id)
    {
        return $this->model::where(strtolower($this->getModelName()). '_id', $id)->first();
    }

    public function delete($id)
    {
        return $this->model::where(strtolower($this->getModelName()).'_id', $id)->delete();
    }

    public function update($id)
    {
        return $this->model::where([strtolower($this->getModelName()).'_id' => $id])->update($this->requestData);
    }

    public function create()
    {
        foreach ($this->requestData as $key => $value)
        {
            $this->model[$key] = $value;
        }

        $this->model->save();
        return $this->model;
    }

    protected function sendResponse($responseCode=200, $responseData=null)
    {
        return response()->json($responseData, $responseCode)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Headers', 'Authorization,Content-Type,X-Requested-With')->header('Access-Control-Allow-Methods', '*');
    }

    public function validation($requestParams, $expectedParams)
    {
        try{
            $validator = Validator::make($requestParams, $expectedParams);
            if ($validator->fails())
            {
                return $validator->errors();
            } 
            else
            {   
                return $validator->errors();
            }
        }
        catch(Exception $e)
        {
            return $e;
        }
    }
}
