<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request as FilterRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_user', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_user', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_user', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $perPage = request('per_page', 15);

        $data =  User::latest()->filter(FilterRequest::only('search', 'trashed'));

        return UserResource::collection($data->paginate($perPage)->appends(FilterRequest::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $fields = $request->all();

        $fields['password'] = Hash::make($request->password);

        $user = User::create($fields);

        $user->assignRole($request->role);

        return response()->noContent();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $fields = $request->all();

        $request->password && $fields['password'] = Hash::make($request->password);

        $user->update($fields);

        DB::table('model_has_roles')->where('model_id', $user->id)->delete();

        $user->assignRole($request->role);

        return response()->noContent();
    }

    public function activation(Request $request, User $user)
    {
        $fields = $request->validate([
            'active' => 'required|boolean',
        ]);

        $user->update($fields);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
