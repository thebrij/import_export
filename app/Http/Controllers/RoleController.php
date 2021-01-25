<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use Auth;
class RoleController extends Controller
{
    /**
     * Display a listing of the roles
     *
     * @param  \App\Models\Role  $model
     * @return \Illuminate\View\View
     */
    public function index(Role $model)
    {
        if(Auth::User()->role_id >= 11 and Auth::User()->role_id <= 13) {
            $roles = $model->paginate(15);
        } else {
            $roles = $model->where('id', '>=', Auth::User()->role_id)->paginate(15);
        }
        return view('roles.index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new role
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\RoleRequest  $request
     * @param  \App\Models\Role  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleRequest $request, Role $model)
    {
        $model->create($request->all());
        return redirect()->route('role.index')->withStatus(__('Role successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\View\View
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage
     *
     * @param  \App\Http\Requests\RoleRequest  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RoleRequest $request, Role  $role)
    {
        $updated = $role->update($request->all());
        if($updated){
            return redirect()->route('role.index')->withStatus(__('Role successfully updated.'));
        } else {
            return view('roles.edit', compact('role'));
        }

    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('role.index')->withStatus(__('Role successfully deleted.'));
    }
}
