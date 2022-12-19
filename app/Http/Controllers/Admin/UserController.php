<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(User $user)
    {
        $this->users=$user;
    }

    public function index()
    {
        return view('admin.users.index',['users'=>$this->users->allGeneralUsers()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $user=$this->users->findOrFail($request->id);

            $user->update(['status'=>$request->status]);

            return redirect()->back()->with('success', 'Status updated successfully!');

        }catch(\Exception $e){
            return back()->with('errors',$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('admin.users.show',['user'=>$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        try{
           return view('admin.users.edit',['user'=>$user]);
        }catch(\Exception $e){
            return back()->with('errors',$e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(User $user,UserUpdateRequest $request)
    {
        try{
            $user->update($request->except('_method','_token','user_role'));

            return redirect()->back()->with('success', 'Updated successfully!');

        }catch(\Exception $e){
            return back()->with('errors',$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try{
            $user->delete();
            return redirect()->back()->with('success', 'Deleted successfully!');

        }catch(\Exception $e){
            return back()->with('errors',$e->getMessage());
        }
    }
}
