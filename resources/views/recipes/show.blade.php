<x-app-layout>
    <div class="container p-4 mx-auto bg-white rounded">
        {{ Breadcrumbs::render('show',$recipe) }}
        <div class="mb-4"></div>    
        <div class="grid grid-cols-2 rounded border border-gray-500">
            <div class="col-span-1">
                <img class="object-cover w-full aspect-square" src="{{$recipe->image}}" alt="{{$recipe->title}}">
            </div>
            <div class="col-span-1 p-4">
                <p class="mb-4">{{ $recipe->description }}</p>
                <p class="mb-4 text-gray-500">{{$recipe->user->name}}</p>
                <h4 class="text-2xl font-bold mb-2">材料</h4>
                <ul class="text-gray-500 ml-6">
                @foreach ($recipe->ingredients as $ingredient)
                    <li>{{$ingredient->name}}:{{$ingredient->quantity}}</li>
                @endforeach
                </ul>
            </div>
        </div>
        <div class="">
            <h4 class="text-2xl font-bold mb-6">作り方</h4>
            <div class="grid grid-cols-4 gap-4">
            @foreach ($recipe->steps as $step)
                <div class="mb-2 background-color p-2 rounded">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-4">{{$step->step_number}}</div>
                    <p>{{$step->description}}</p>
                </div>
            @endforeach
            </div>
        </div>
    </div>
    @if($is_my_recipe)
    <a href="{{route('recipe.edit',['id'=>$recipe->id])}}" class="block bg-green-600 text-white p-2 px-4 mx-auto rounded w-2/12 my-4 text-center">編集</a>
    @endif
    @guest
    <div class="container mt-6 p-4 mx-auto bg-white rounded">
        <p class="text-gray-500 text-center">レビューを投稿するには<a class="text-blue-700" href="{{route('login')}}" >ログイン</a>してください</p>
    </div>
    @endguest
    @auth
        @if ($is_reviewed)
            <p class="text-center text-gray-500">レビューは投稿済みです</p>
        @elseif($is_my_recipe)
            <p class="text-center text-gray-500">自分のレシピにはレビューできません</p>
        @else
            <div class="container mt-6 p-4 mx-auto bg-white rounded">
                <form action="{{route('review.store',['id' => $recipe->id])}}" method="POST">
                    @csrf
                    <input type="hidden" name="recipe_id" value="{{$recipe->id}}">
                    <div class="mb-4">
                        <label for="rating" class="block text-gray-700 font-bold mb-2">評価</label>
                        <select name="rating" id="rating" class="border border-gray-300 p-2 w-full rounded">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3" selected>3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <label for="comment" class="block text-gray-700 font-bold mb-2 mt-4">コメント</label>
                        <textarea name="comment" id="comment" class="border border-gray-300 p-2 w-full rounded" placeholder="コメントを入力"></textarea>
                        <button type="submit" class="bg-green-600 text-white p-2 px-4 rounded mb-4 text-right mt-4">レビューを投稿する</button>
                    </div>
                </form>
            </div>
        @endif
    @endauth
    <div class="container mt-6 p-4 mx-auto bg-white rounded">
        <h4 class="text-2xl font-bold mb-2">レビュー</h4>
        @if(count($recipe->reviews) === 0)
            <p>レビューはまだありません</p>
        @endif
        @foreach ($recipe->reviews as $review)
            <div class="background-color rounded mb-4 p-4">
                <div class="flex first-line:mb-4">
                    @for($i = 0; $i < $review->rating; $i++)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                      </svg>
                      
                    @endfor
                    <p class="ml-2">{{$review->comment}}</p>
                </div>
                <p class="font-bold">{{$review->user->name}}</p>
            </div>
        @endforeach
    </div>
</x-app-layout>