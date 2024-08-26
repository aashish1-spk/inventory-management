<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SubCategory;



class SubCategoryController extends Controller
{
    public function index(Request $request)
{
    $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
    ->latest('sub_categories.id')
    ->leftjoin('categories','categories.id',
    'sub_categories.category_id');

    if (!empty($request->get('keyword'))) {
        $subCategories = $subCategories->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
    
        $subCategories = $subCategories->orwhere('categories.name', 'like', '%' . $request->get('keyword') . '%');

    
    }


    $subCategories = $subCategories->paginate(10);

    return view('admin.sub_category.list', compact('subCategories'));
}


    public function create() {
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;  // Remove the '$' from the key
        return view('admin.sub_category.create', $data);
    }


    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);
    
        // Check if the validation passes
        if ($validator->passes()) {
            
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();


            $request->session()->flash('success','Sub Category created successfully');

            return response([
                'status' => true,
                'message' => 'Sub Category created successfully'
            ]);
            

        } else {
            // If validation fails, return the errors
            return response([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    

        
}
    

