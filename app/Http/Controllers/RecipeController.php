<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RecipeCreateRequest;
use App\Http\Requests\RecipeUpdateRequest;
use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Step;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function home(){

        $recipes = Recipe::select('recipes.id', 'recipes.title', 'recipes.description', 'recipes.created_at','recipes.image','users.name')
            ->join('users', 'users.id' , '=', 'recipes.user_id')
            ->orderBy('recipes.created_at', 'desc')
            ->limit(3)
            ->get();
            //dd($recipes);

        $popular = Recipe::select('recipes.id', 'recipes.title', 'recipes.description', 'recipes.created_at','recipes.image','users.name')
            ->join('users', 'users.id' , '=', 'recipes.user_id')
            ->orderBy('recipes.views', 'desc')
            ->limit(2)
            ->get();
            //dd($popular);

        return view('home',compact('recipes','popular'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        //dd($filters);
        $query = Recipe::query()->select('recipes.id', 'recipes.title', 'recipes.description', 'recipes.created_at','recipes.image','users.name',\DB::raw('AVG(reviews.rating) as rating'))
            //->where('recipes.deleted_at', null)
            ->join('users', 'users.id' , '=', 'recipes.user_id')
            ->leftJoin('reviews','reviews.recipe_id','=','recipes.id')
            ->groupBy('recipes.id')
            ->orderBy('recipes.created_at', 'desc');
        if(!empty($filters)){
            // AND検索で絞り込み
            //もしカテゴリーが選択されていたら
            if(!empty($filters['categories'])){
                // カテゴリーで絞り込み選択したカテゴリーIDが含まれているレシピを取得
                $query->whereIn('recipes.category_id', $filters['categories']);
            }
            //もし評価が入力されていたら
            if(!empty($filters['rating'])){
                // 評価の「平均」で絞り込み 0 or 3 or 4以上
                $query->havingRaw('AVG(reviews.rating) >= ?', [$filters['rating']]);
            }
            //もしタイトルが入力されていたら
            if(!empty($filters['title'])){
                // タイトルで絞り込み　前後あいまい検索
                $query->where('recipes.title','like', '%'.$filters['title'].'%');
            }
        }
        $recipes = $query->paginate(10);
        //dd($recipes);

        $categories = Category::all();

        return view('recipes.index',compact('recipes','categories','filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('recipes.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecipeCreateRequest $request)
    {
        $posts = $request->all();
        //dd($posts);
        $uuid = Str::uuid()->toString();
        
        $image = $request->file('image');
        // Amazon S3に画像をアップロード
        $path = Storage::disk('s3')->putFile('recipe', $image, 'public');
        //dd($path);
        // S3のURLを取得
        $url = Storage::disk('s3')->url($path);
        // DBにはURLを保存

        // トランザクション開始
        try{
            DB::beginTransaction();
            Recipe::insert([
                'id' => $uuid,
                'title' => $posts['title'],
                'description' => $posts['description'],
                'category_id' => $posts['category_id'],
                'image' => $url, 
                'user_id' => Auth::id(),
            ]);

            $ingredients = [];            

            if(!empty($posts['ingredients'])){
                foreach($posts['ingredients'] as $key => $ingredient){
                    $ingredients[$key] = [
                        'recipe_id' => $uuid,
                        'name' => $ingredient['name'],
                        'quantity' => $ingredient['quantity']
                    ];
                }
            } else {
                $posts['ingredients'] = [];
            }
            
            //dd($ingredients);
            Ingredient::insert($ingredients);

            $steps = [];
            if(!empty($posts['steps'])){
                foreach($posts['steps'] as $key => $step){
                    $steps[$key] = [
                        'recipe_id' => $uuid,
                        'step_number' => $key + 1,
                        'description' => $step,
                    ];
                }
            } else {
                $posts['steps'] = [];
            }
            
            //dd($steps);
            Step::insert($steps);
            DB::commit();
        }catch(\Throwable $th){
            DB::rollback();
            \Log::debug(print_r($th->getMessage(),true));
            throw $th;
        }

        flash()->success('レシピを投稿しました！');
        return redirect()->route('recipe.show', ['id' => $uuid]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $recipe = Recipe::with(['ingredients','steps','reviews.user','user'])
            //リレーションで材料とステップ,レビューを取得
            ->where('recipes.id',$id)
            ->firstOrFail();
        $recipe_record = Recipe::find($id);
        $recipe_record->increment('views');
        //dd($recipe);

        $is_my_recipe = false;
        if(Auth::check() && ($recipe->user_id === Auth::id())){
            $is_my_recipe = true;
        }

        $is_reviewed = false;
        if(Auth::check()){
            $is_reviewed = $recipe->reviews->contains('user_id',Auth::id());
            // foreach($recipe->reviews as $review){
            //     if($review->user_id === Auth::id()){
            //         $is_reviewed = true;
            //     }
            // }
        }

        return view('recipes.show',compact('recipe','is_my_recipe','is_reviewed'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $recipe = Recipe::with(['ingredients','steps','reviews.user','user'])
            //リレーションで材料とステップ,レビューを取得
            ->where('recipes.id',$id)
            ->firstOrFail();
        if(!Auth::check() || (Auth::id() !== $recipe->user_id)){
            abort(403);
        }

        $categories = Category::all();

        return view('recipes.edit',compact('recipe','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecipeUpdateRequest $request, string $id)
    {
        $posts = $request->all();
        // 画像の更新がある場合
        $update_array = [
            'title' => $posts['title'],
            'description' => $posts['description'],
            'category_id' => $posts['category_id'],
        ];
        if($request->file('image')){
            $image = $request->file('image');
            // Amazon S3に画像をアップロード
            $path = Storage::disk('s3')->putFile('recipe', $image, 'public');
            // S3のURLを取得
            $url = Storage::disk('s3')->url($path);
            // DBにはURLを保存
            $update_array['image'] = $url;
        }
        try{
            DB::beginTransaction();
            //dd($posts);
            Recipe::where('id',$id)->update($update_array);            
            Ingredient::where('recipe_id',$id)->delete();
            Step::where('recipe_id',$id)->delete();

            $ingredients = [];            
            if(!empty($posts['ingredients'])){
                foreach($posts['ingredients'] as $key => $ingredient){
                    $ingredients[$key] = [
                        'recipe_id' => $id,
                        'name' => $ingredient['name'],
                        'quantity' => $ingredient['quantity']
                    ];
                }
            } else {
                $posts['ingredients'] = [];
            }
            //dd($ingredients);
            Ingredient::insert($ingredients);

            $steps = [];
            if(!empty($posts['steps'])){
                foreach($posts['steps'] as $key => $step){
                    $steps[$key] = [
                        'recipe_id' => $id,
                        'step_number' => $key + 1,
                        'description' => $step,
                    ];
                }
            } else {
                $posts['steps'] = [];
            }
            //dd($steps);
            Step::insert($steps);
            DB::commit();
        } catch(\Throwable $th){
            DB::rollback();
            \Log::debug(print_r($th->getMessage(),true));
            throw $th;
        }
        
        flash()->success('レシピを更新しました！');
        return redirect()->route('recipe.show', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Recipe::where('id',$id)->delete();
        //Recipe::where('id',$id)->update(['deleted_at' => now()]);

        flash()->warning('レシピを削除しました！');
        return redirect()->route('home');
    }
}
