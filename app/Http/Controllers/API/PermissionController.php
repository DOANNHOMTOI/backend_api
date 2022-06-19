<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{

    public function index(Request $request)
    {
        return $this->sendResponse(Permission::paginate(env('APP_LIMIT_PAGE')),'success');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        if ( Permission::where('name',$request->name)->first() != null){
            return $this->sendError('Tên quyền đã tổn tại !', 'error');
        }
        $permission = new Permission();
        $permission->name = trim($request->name);
        $permission->save();

        return $this->sendResponse($permission, 'success');
    }
    public function detail($id){
        return $this->sendResponse(Permission::find($id), 'success');
    }
    public function delete($id){
        Permission::find($id)->delete();
        return $this->sendResponse($id, 'success');
    }
    public function update(Request $request,$id){
        $permission = Permission::find($id);
        $permission->name = trim($request->title);
        $permission->save();
        return $this->sendResponse($permission, 'success');
    }
}
