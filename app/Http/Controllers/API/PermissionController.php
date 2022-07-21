<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\PermissionUser;
use App\ProductCategory;
use App\Permission;
use App\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->page = -1 ? 1000000 : env('APP_LIMIT_PAGE');
        return $this->sendResponse(Permission::paginate($limit),'success');
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

    public function assign(Request $request){
        $user_id = $request->user;
        $listPermission = explode(",", $request->permissions);

        PermissionUser::where('user_id', $user_id)->delete();

        foreach ($listPermission as $value) {
            $pu = new PermissionUser();
            $pu->user_id = $user_id;
            $pu->permission_id = $value;
            $pu->save();
        }
        return $this->sendResponse('success', 'success');
    }
    public function permissionByUser(Request $request,$id){
        $list = PermissionUser::where('user_id',$id)->get();
        $arr['name'] = [];
        $arr['id'] = [];
        foreach ($list as $k=>$v){
            $p = Permission::find($v->permission_id);
            array_push( $arr['name'],$p->name);
            array_push( $arr['id'],$p->id);
        }
        return $this->sendResponse($arr, 'success');
    }
}
