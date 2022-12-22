<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ItemResourceCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Item;
use App\Models\ItemAuthor;
use App\Models\ItemThumbnail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB,Hash,Validator,Auth;

class ItemController extends Controller
{
    use ApiResponseTrait;

    public function __construct(Item $item)
    {
        $this->model=$item;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $items=$this->model->with('itemAuthors','itemThumbnails')->latest('sequence','DESC')->get();
            return $this->respondWithSuccess('All Item list',ItemResourceCollection::make($items),Response::HTTP_OK);
        }catch(Exception $e){
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
        return $this->respondWithSuccess('Item max serial',['sequence'=>$maxSequence],Response::HTTP_OK);
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

        DB::beginTransaction();
        try{
            $input=$request->all();

            // Brochure upload ------
            if ($request->hasFile('brochure'))
            {
                $input['brochure']=\MyHelper::fileUpload($request->file('brochure'),'files/items/brochure');
            }

            $item=$this->model->create($input);
            //$item=$this->model->latest()->first();

            // Photo upload ------
            if ($request->hasFile('image')){
                $this->multiPhotoUpload($request, $item->id,false);
            }

            // Item Author ------
            if ($request->has('author_id') && count($request->author_id)>0)
            {
                $this->storeItemAuthors($request,$item->id,false);
            }

            DB::commit();
            return $this->respondWithSuccess('Item has been created successful',new  ItemResource($item),Response::HTTP_OK);

        }catch(\Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeValidationRules($request){
        return [
            'title' => 'required|max:200|unique:items,title,NULL,id,deleted_at,NULL',
            'isbn'  => "nullable|max:100",
            'edition'  => "nullable:max:100",
            'number_of_page'  => "nullable|max:50",
            'summary'  => "nullable|max:255",
            'video_url'  => "nullable|max:255",
            'brochure'  => "nullable|mimes:pdf|max:10000",
            'publisher_id'  => "nullable|exists:publishers,id",
            'language_id'  => "nullable|exists:languages,id",
            'country_id'  => "nullable|exists:countries,id",
            'category_id'  => "nullable|exists:categories,id",
            'subcategory_id'  => "nullable|exists:sub_categories,id",
            'third_category_id'  => "nullable|exists:third_sub_categories,id",
            "author_id"   => "required|array|min:1",
            'author_id.*' => "exists:authors,id",
            'status'  => "required|in:1,2",
            'sequence'  => "required",
            //'photo' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:8048'
        ];
    }
    public function multiPhotoUpload($request,$itemId,$update=false)
    {
        $itemImages=[];
        foreach ($request->image as $key=>$imageData) {

            if ($update == true) {
                $oldImage = ItemThumbnail::where(['id' => $key, 'item_id' => $itemId])->first();

                if (!empty($oldImage))
                {
                    if ($oldImage->big != null and file_exists($oldImage->big)) {
                        unlink($oldImage->big);
                    }

                    if ($oldImage->medium != null and file_exists($oldImage->medium)) {
                        unlink($oldImage->medium);
                    }

                    if ($oldImage->small != null and file_exists($oldImage->small)) {
                        unlink($oldImage->small);
                    }

                    $imagePaths = \MyHelper::multiPhotoUpload($imageData, 'images/items/big', 750);

                    $oldImage->update([
                        'big' => $imagePaths['big'],
                        'medium' => $imagePaths['medium'],
                        'small' => $imagePaths['small'],
                    ]);
                }else{
                    $imagePaths = \MyHelper::multiPhotoUpload($imageData, 'images/items/big', 750);

                    $is_thumbnail = ItemThumbnail::NO;
                    if ($key == 0) {
                        $is_thumbnail = ItemThumbnail::YES;
                    }

                    $itemImages[] = [
                        'item_id' => $itemId,
                        'big' => $imagePaths['big'],
                        'medium' => $imagePaths['medium'],
                        'small' => $imagePaths['small'],
                        'is_thumbnail' => $is_thumbnail,
                    ];
                }


            }else{

                $imagePaths = \MyHelper::multiPhotoUpload($imageData, 'images/items/big', 750);

                $is_thumbnail = ItemThumbnail::NO;
                if ($key == 0) {
                    $is_thumbnail = ItemThumbnail::YES;
                }

                $itemImages[] = [
                    'item_id' => $itemId,
                    'big' => $imagePaths['big'],
                    'medium' => $imagePaths['medium'],
                    'small' => $imagePaths['small'],
                    'is_thumbnail' => $is_thumbnail,
                ];
            }

        }

        if (count($itemImages)>0)
        {
            ItemThumbnail::insert($itemImages);
        }
    }

    public function storeItemAuthors($request,$itemId,$update=false)
    {
        if ($update==true)
        {
            ItemAuthor::where('item_id',$itemId)->delete();
        }

        foreach ($request->author_id as $key=>$author_id)
        {
            $itemAuthors[]=[
                'item_id'=>$itemId,
                'author_id'=>$author_id,
            ];
        }
        ItemAuthor::insert($itemAuthors);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $item=$this->model->with('itemAuthors','itemThumbnails')->where('id',$id)->first();
            if ($item){
                return $this->respondWithSuccess('Item Info',new  ItemResource($item),Response::HTTP_OK);
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
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item=$this->model->where('id',$id)->first();
        if (!$item){
            return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
        }

        $rules=$this->updateValidationRules($request,$id);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidation('Validation Fail',$validator->errors()->first(),Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try{
            $input=$request->all();

            // Brochure upload ------
            if ($request->hasFile('brochure'))
            {
                if ($item->brochure != null and file_exists($item->brochure)) {
                    unlink($item->brochure);
                }
                $input['brochure']=\MyHelper::fileUpload($request->file('brochure'),'files/items/brochure');
            }

            $item->update($input);
            //$item=$this->model->latest()->first();

            // Photo (Update)upload ------
            if ($request->hasFile('image')){
                $this->multiPhotoUpload($request, $item->id,true);
            }

            // Item Author ------
            if ($request->has('author_id') && count($request->author_id)>0)
            {
                $this->storeItemAuthors($request,$item->id,true);
            }

            DB::commit();
            return $this->respondWithSuccess('Item has been updated successful',new  ItemResource($item),Response::HTTP_OK);

        }catch(Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateValidationRules($request,$itemId){
        return [
            'title' => "required|max:200|unique:items,title,$itemId,id,deleted_at,NULL",
            'isbn'  => "nullable|max:100",
            'edition'  => "nullable:max:100",
            'number_of_page'  => "nullable|max:50",
            'summary'  => "nullable|max:255",
            'video_url'  => "nullable|max:255",
            'brochure'  => "nullable|mimes:pdf|max:10000",
            'publisher_id'  => "nullable|exists:publishers,id",
            'language_id'  => "nullable|exists:languages,id",
            'country_id'  => "nullable|exists:countries,id",
            'category_id'  => "nullable|exists:categories,id",
            'subcategory_id'  => "nullable|exists:sub_categories,id",
            'third_category_id'  => "nullable|exists:third_sub_categories,id",
            "author_id"   => "required|array|min:1",
            'author_id.*' => "exists:authors,id",
            'status'  => "required|in:1,2",
            'sequence'  => "required",
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{

            $item=$this->model->where('id',$id)->first();

            if (!$item){
                return $this->respondWithError('No data found',[],Response::HTTP_NOT_FOUND);
            }

            // Delete Item Author ----------------
            ItemAuthor::where('item_id',$id)->delete();

            $oldImages = ItemThumbnail::where(['item_id' => $id])->get();

            if (count($oldImages)>0)
            {
                foreach ($oldImages as $key=>$oldImage){

                    if ($oldImage->big != null and file_exists($oldImage->big)) {
                        unlink($oldImage->big);
                    }

                    if ($oldImage->medium != null and file_exists($oldImage->medium)) {
                        unlink($oldImage->medium);
                    }

                    if ($oldImage->small != null and file_exists($oldImage->small)) {
                        unlink($oldImage->small);
                    }
                    // Delete Item Image ----------------
                    $oldImage->delete();
                }
            }

            // Delete Item ----------------
            $item->delete();


            DB::commit();
            return $this->respondWithSuccess('Item has been Deleted',[],Response::HTTP_OK);
        }catch(\Exception $e){
            DB::rollback();
            return $this->respondWithError('Something went wrong, Try again later',$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
