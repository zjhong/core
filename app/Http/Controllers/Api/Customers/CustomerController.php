<?php


namespace App\Http\Controllers\Api\Customers;


use App\Http\Controllers\Api\Controller;
use App\Models\Customers\Customer;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Get User List
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $post         = request()->all();
        $customerList = new Customer();
        $limit        = 30;
        if (isset($post['search'])) {
            $customerList = $customerList->where('email', 'like', '%' . $post['search'] . '%');
        }
        if (isset($post['limit']) && is_numeric($post['limit'])) {
            $limit = $post['limit'];
        }
        $customerList = $customerList->paginate($limit);
        return $this->jsonResponse(200, 'success', $customerList);
    }

    /**
     * Add Customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $validator = Validator::make(request()->all(), [
            'title' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(1011, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $customer = Customer::create($post);
        return $this->jsonResponse(200, 'success', $customer);
    }

    /**
     * Edit Customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit()
    {
        if (!request()->has('id')) {
            return $this->jsonResponse(1012, 'not found id, mast post id');
        }
        $validator = Validator::make(request()->all(), [
            'name'  => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
        ]);
        if ($validator->fails()) {
            return $this->jsonResponse(1013, 'error', [], $validator->errors());
        }
        $post     = request()->all();
        $customer = Customer::find($post['id']);
        if (!$customer) {
            return $this->jsonResponse(1014, 'customer does not exist');
        }
        $customer = $customer->update($post);
        return $this->jsonResponse(200, 'success', $customer);
    }

    /**
     * Delete Customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        if (!request()->has('id')) {
            return $this->jsonResponse(1012, 'not found id, mast post id');
        }
        $customer = Customer::find(request('id'));
        if (!$customer) {
            return $this->jsonResponse(1014, 'customer does not exist');
        }
        $customer->delete();
        return $this->jsonResponse(200, 'success');
    }
}
