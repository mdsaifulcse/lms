<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublisherResource;
use App\Http\Resources\PublisherResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class PublisherController extends Controller
{
    use ApiResponseTrait;

    public function __construct(Publisher $publisher)
    {
        $this->model=$publisher;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $publishers=$this->model->latest('sequence','DESC')->get();
            return $this->respondWithSuccess('All Publisher list',PublisherResourceCollection::make($publishers),Response::HTTP_OK);
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

        return $this->respondWithSuccess('Publisher max serial',['sequence'=>$maxSequence],Response::HTTP_OK);
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

            if ($request->establish){
                $input['establish']=date("d-m-Y", strtotime($request->establish));
            }

            // Photo upload ------
            $photo='';
            if ($request->hasFile('photo')){
                $photo=\MyHelper::photoUpload($request->file('photo'),'images/publisher-photo',150);
                $input['photo']=$photo;
            }
            $publisher=$this->model->create($input);
            return $this->respondWithSuccess('Publisher Info has been created successful',new  PublisherResource($publisher),Response::HTTP_OK);

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
            'establish'  => "nullable|max:15",
            'status'  => "required|in:0,1",
            'sequence'  => "required",
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $publisher=$this->model->where('id',$id)->first();
            if ($publisher){
                return $this->respondWithSuccess('Publisher Info',new  PublisherResource($publisher),Response::HTTP_OK);
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
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function edit(Publisher $publisher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Publisher  $publisher
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
            $publisher=$this->model->where('id',$id)->first();

            if (!$publisher){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            $input=$request->all();
            if ($request->establish){
                $input['establish']=date("d-m-Y", strtotime($request->establish));
            }
            // Photo upload ------
            $photo=$publisher->photo;
            if ($request->hasFile('photo')){
                if($publisher->photo!=null and file_exists($publisher->photo)){ unlink($publisher->photo);}

                $photo=\MyHelper::photoUpload($request->file('photo'),'images/publisher-photo',150);
                $input['photo']=$photo;
            }

            $publisher->update($input);

            return $this->respondWithSuccess('Publisher Info has been updated successful',new  PublisherResource($publisher),Response::HTTP_OK);

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
            'status'  => "required|in:0,1",
            'sequence'  => "required",
            'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $publisher=$this->model->where('id',$id)->first();
            if ($publisher){
                $publisher->delete();
                return $this->respondWithSuccess('Publisher has been Deleted',[],Response::HTTP_OK);
            }else{
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

        }catch(\Exception $e){
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
