<?php

namespace App\Http\Controllers;

use App\Http\Requests\createCategory;
use App\Http\Requests\updateCategory;
use App\Http\Response\BaseResponse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function create(createCategory $request)
    {
        $category = Category::firstOrCreate($request->validated());
        if($category){
            return BaseResponse::success('Create successfully!');
        }
        return BaseResponse::error("Error Create!");
    }

    public function update(updateCategory $request, Category $category)
    {
        if($category->update($request->validated())){
            return BaseResponse::success('Update successfully!');
        }

        return BaseResponse::error('Error Update Data!');
    }

    public function delete(Category $category)
    {
        if($category->delete()){
            return BaseResponse::success('Delete successfully!');
        }
        return BaseResponse::error('Error Delete Data!');
    }

    public function getById(Category $category)
    {
        return BaseResponse::success($category);
    }

    public function getByFilter(Request $request)
    {
        $query = Category::query();
        // Фильтрация по полю name // Фильтрация по полю description
        if ($request->has('search')) {
            $searchTerm = str_replace('е', 'ё', mb_strtolower($request->input('search')));
            $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . $searchTerm . '%');
            $query->orwhere(DB::raw('LOWER(description)'), 'LIKE', '%' . $searchTerm . '%');
        }

        // Фильтрация по полю active
        if ($request->has('active')) {
            $active = $request->input('active') === '1' || $request->input('active') === 'true';
            $query->where('active', $active);
        }
        // Сортировка
        $sortField = $request->input('sort', '-created_at');
        $sortDirection = Str::startsWith($sortField, '-') ? 'desc' : 'asc';
        $sortField = ltrim($sortField, '-');

        if (in_array($sortField, ['name', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        // Пагинация
        $pageSize = $request->input('pageSize', 2);
        $categories = $query->paginate($pageSize);
        if ($request->has('search')) {
            $categories = $categories->appends(['search'=>$request->input('search')]);
        }
        if ($request->has('pageSize')) {
            $categories = $categories->appends(['pageSize'=>$request->input('pageSize')]);
        }
        return BaseResponse::success($categories);
    }
}
