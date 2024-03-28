<x-app-layout>
    <x-slot name="script">
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script src="/js/recipe/create.js"></script>
    </x-slot>
    <form action="{{route('recipe.store')}}" method="POST" class="container p-4 mx-auto bg-white rounded" enctype="multipart/form-data">
        @csrf
        {{ Breadcrumbs::render('create') }}
        <div class="mb-4"></div>
        <div class="grid grid-cols-2 rounded border border-gray-500">
            <div class="col-span-1">
                <img id="preview" class="object-cover w-full aspect-video" src="/images/recipe-dummy.png" alt="recipe-image">
                <input type="file" id="image" name="image" class="border border-gray-300 p-2 mb-4 w-full rounded">
            </div>
            <div class="col-span-1 p-4">
                <input type="text" name="title" class="border border-gray-300 rounded p-2 mb-4 w-full" placeholder="レシピ名を入力" value="{{old('title')}}">
                <textarea name="description" class="border border-gray-300 p-2 mb-4 w-full rounded" placeholder="レシピの説明を入力">{{old('description')}}</textarea>
                <select name="category_id" class="border border-gray-300 p-2 mb-4 w-full rounded">
                    <option value="">カテゴリを選択</option>
                    @foreach ($categories as $category)
                        <option value="{{$category->id}}" {{(old('category_id') ?? null) == $category->id ? 'selected' : ''}}>{{$category->name}}</option>
                    @endforeach
                </select>
                <h4 class="text-2xl font-bold mb-2">材料を入力</h4>
                <div id="ingredients" class="mb-4">
                    @php
                        $old_ingredients = old('ingredients') ?? null;
                    @endphp
                    @if(is_null($old_ingredients))
                        @for ($i = 0; $i < 3; $i++)     
                        <div class="flex mb-2 items-center ingredient">
                            @include('components.bars-3')
                            <input type="text" name="ingredients[{{$i}}][name]" class="ingredient-name ml-4 border border-gray-300 p-2 w-1/2 rounded" placeholder="材料名">
                            <p class="mx-2">:</p>
                            <input type="text" name="ingredients[{{$i}}][quantity]" class="ingredient-quantity border border-gray-300 p-2 w-1/2 rounded" placeholder="分量">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 ml-4 ingredient-delete cursor-pointer text-gray-500">
                                <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                            </svg>    
                        </div>
                        @endfor
                    @else
                        @foreach ($old_ingredients as $i => $oi)
                        <div class="flex mb-2 items-center ingredient">
                            @include('components.bars-3')
                            <input type="text" name="ingredients[{{$i}}][name]" value="{{$oi['name']}}" class="ingredient-name ml-4 border border-gray-300 p-2 w-1/2 rounded" placeholder="材料名">
                            <p class="mx-2">:</p>
                            <input type="text" name="ingredients[{{$i}}][quantity]" value="{{$oi['quantity']}}" class="ingredient-quantity border border-gray-300 p-2 w-1/2 rounded" placeholder="分量">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 ml-4 ingredient-delete cursor-pointer text-gray-500">
                                <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                            </svg>    
                        </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" id="add-ingredient" class="bg-gray-600 text-white p-2 px-4 rounded">材料を追加</button>
            </div>
        </div>
        <div class="flex justify-center mt-4">
            <button type="submit" class="bg-green-600 text-white p-2 px-4 rounded mb-4 text-right">レシピを投稿する</button>
        </div>
        <hr class="my-4">
        <h4 class="text-center font-bold text-lg mb-4">手順を入力</h4>
        <div id="steps">
            @php
                $old_steps = old('steps') ?? null;
            @endphp
            @if (is_null($old_steps))
                @for ($i = 1; $i < 4; $i++)
                <div class="step flex justify-between items-center mb-2">
                    @include('components.bars-3')
                    <p class="step-number w-20 text-center">手順{{$i}}</p>                  
                    <input type="text" name="steps[]" class="border border-gray-300 p-2 w-full rounded" placeholder="手順を入力">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 ml-4 step-delete cursor-pointer text-gray-500">
                        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                    </svg>                  
                </div>
                @endfor
            @else
                @foreach ($old_steps as $i => $os)
                <div class="step flex justify-between items-center mb-2">
                    @include('components.bars-3')
                    <p class="step-number w-20 text-center">手順{{$i+1}}</p>                  
                    <input type="text" name="steps[]" class="border border-gray-300 p-2 w-full rounded" placeholder="手順を入力" value="{{$os}}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 ml-4 step-delete cursor-pointer text-gray-500">
                        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                    </svg>                  
                </div>
                @endforeach
            @endif
            
        </div>
        <button type="button" id="add-step" class="bg-gray-600 text-white p-2 px-4 rounded">手順を追加</button>
    </form>
</x-app-layout>