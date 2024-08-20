<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;




class CategoryController extends Controller
{
   public function index(Request $request) {
        $categories = Category::latest();
        
        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        


        $categories = $categories->paginate(10);
        $data['categories'] = $categories;

        return view('admin.category.list',compact('categories'));
   }

   public function create() {
        return view('admin.category.create');
   }

   public function store(Request $request) {
        // Corrected syntax for Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()) {
            
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            //Save The Image
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);
                
               

                
                $category->image = $newImageName;
                $category->save();
            }
            

            

            $request->session()->flash('success','Category added successfully');


            return response()->json([
                'status' => true,  
                'message' => 'Category added successfully'
            ]);



        } else {
            return response()->json([
                'status' => false,  
                'errors' => $validator->errors(),
            ]);
        }
   }

   public function edit() {
        // Logic for editing categories
   }

   public function update() {
        // Logic for updating categories
   }

   public function destroy() {
        // Corrected spelling from 'distroy' to 'destroy'
        // Logic for deleting categories
   }
}
