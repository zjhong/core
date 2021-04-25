<?php


namespace App\Http\Controllers\Api\Users;


use App\Http\Controllers\Api\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get User List
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $post      = request()->all();
        $usersList = new User();
        $limit     = 10;
        if (isset($post['search'])) {
            $usersList = $usersList->where('name', 'like', '%' . $post['search'] . '%');
        }
        if (isset($post['limit']) && is_numeric($post['limit'])) {
            $limit = $post['limit'];
        }
        $usersList = $usersList->paginate($limit);
        return $this->jsonResponse(200, 'success', $usersList);
    }

    /**
     * Add User
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $validator = Validator::make(request()->all(), [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile'      => ['required','max:11', 'unique:users'],
            'remark'      => ['max:255'],
            'password'    => ['required', 'min:6', 'confirmed'],
            'enabled'     => ['boolean'],
            'customer_id' => ['uuid'],
            'is_admin' => ['required'],
            'business_id' => ['required']
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(1001, 'error', $validator->errors());
        }
        $post             = request()->all();
        $post['enabled']  = $post['enabled'] ?? 1;
        $post['password'] = Hash::make($post['password']);
        $user             = User::create($post);
        return $this->jsonResponse(200, 'success', $user);
    }

    /**
     * Edit User
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit()
    {
        if (!request()->has('id')) {
            return $this->jsonResponse(1002, 'not found id, mast post id');
        }
        $validator = Validator::make(request()->all(), [
            'name'     => ['string', 'max:255'],
            'email'    => ['string', 'email', 'max:255'],
            'mobile'      => ['max:11'],
            'remark'      => ['max:255'],
            'is_admin' => ['required'],
            'business_id' => ['required']
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(1003, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $user = User::find($post['id']);
        if (!$user) {
            return $this->jsonResponse(1004, 'user does not exist');
        }
        $user->update($post);
        $user = User::find($post['id']);
        return $this->jsonResponse(200, 'success', $user);
    }

    public function update(){
        if (!request()->has('id')) {
            return $this->jsonResponse(1002, 'not found id, mast post id');
        }
        $validator = Validator::make(request()->all(), [
            'password'    => ['required', 'min:6', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(1003, 'error', [], $validator->errors());
        }
        $post = request()->all();
        $user = User::find($post['id']);
        if (!$user) {
            return $this->jsonResponse(1004, 'user does not exist');
        }
        $post['enabled']  = $post['enabled'] ?? 1;
        $post['password'] = Hash::make($post['password']);
        $user->update($post);
        $user = User::find($post['id']);
        return $this->jsonResponse(200, 'success', $user);
    }

    /**
     * Delete User
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        if (!request()->has('id')) {
            return $this->jsonResponse(1002, 'not found id, mast post id');
        }
        $user = User::find(request('id'));
        if (!$user) {
            return $this->jsonResponse(1004, 'user does not exist');
        }
        $user->delete();
        return $this->jsonResponse(200, 'success');
    }

    /**
     * Add User Permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function permission()
    {
        if (!request()->has('id')) {
            return $this->jsonResponse(1002, 'not found id, mast post id');
        }
        if (!request()->has('permission')) {
            return $this->jsonResponse(1005, 'not found permission, mast post permission');
        }
        $user = User::find(request('id'));
        if (!$user) {
            return $this->jsonResponse(1004, 'user does not exist');
        }
        if (!is_array(request('permission'))) {
            return $this->jsonResponse(1006, 'permission must be array');
        }
        $user->syncPermissions(request('permission'));
        return $this->jsonResponse(200, 'success');
    }
}
