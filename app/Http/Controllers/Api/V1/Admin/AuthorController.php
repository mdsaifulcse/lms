<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Author;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class AuthorController extends Controller
{
    use ApiResponseTrait;

    public function __construct(Author $author)
    {
        $this->model=$author;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $authors=$this->model->latest('sequence','DESC')->get();
            return $this->respondWithSuccess('All Author list',AuthorResourceCollection::make($authors),Response::HTTP_OK);
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMaxSequence()
    {
        $maxSequence=$this->model::max('sequence')+1;

        return $this->respondWithSuccess('Author max serial',['sequence'=>$maxSequence],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules=$this->storeValidationRules($request);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        try{
            $input=$request->all();
            // Photo upload ------
            $photo='';
            if ($request->hasFile('photo')){
                $photo=\MyHelper::photoUpload($request->file('photo'),'images/author-photo',150);
                $input['photo']=$photo;
            }
            $author=$this->model->create($input);
            return $this->respondWithSuccess('Author Info has been updated successful',new  AuthorResource($author),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
       return [
            'name' => 'required|max:100',
            'email'  => "nullable|max:80",
            'mobile'  => "nullable:max:15",
            'contact'  => "nullable|max:50",
            'address1'  => "nullable|max:800",
            'address2'  => "nullable|max:800",
            'status'  => "required|in:1,2",
            'sequence'  => "required",
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $author=$this->model->where('id',$id)->first();
            if ($author){
                return $this->respondWithSuccess('Author Info',new  AuthorResource($author),Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }
        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules=$this->updateValidationRules($request);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        try{
            $author=$this->model->where('id',$id)->first();

            if (!$author){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            $input=$request->all();
            // Photo upload ------
            $photo=$author->photo;
            if ($request->hasFile('photo')){
                if($author->photo!=null and file_exists($author->photo)){ unlink($author->photo);}

                $photo=\MyHelper::photoUpload($request->file('photo'),'images/author-photo',150);
                $input['photo']=$photo;
            }

            $author->update($input);

            return $this->respondWithSuccess('Author Info has been updated successful',new  AuthorResource($author),Response::HTTP_OK);

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateValidationRules($request){
        return [
            'name' => 'required|max:100',
            'email'  => "nullable|max:80",
            'mobile'  => "nullable:max:15",
            'contact'  => "nullable|max:50",
            'address1'  => "nullable|max:800",
            'address2'  => "nullable|max:800",
            'status'  => "required|in:1,2",
            'sequence'  => "required",
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $author=$this->model->where('id',$id)->first();
            if ($author){
                $author->delete();
                return $this->respondWithSuccess('Author has been Deleted',[],Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
