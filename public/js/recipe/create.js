window.onload = function() {
    // preview image
    var preview = document.getElementById('preview');
    var image = document.getElementById('image');
    image.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // steps sortable
    var steps = document.getElementById('steps');

    Sortable.create(steps, {
        animation: 150,
        handle: '.handle', 
        onEnd: function (evt) {
            var items = steps.querySelectorAll('.step');
            items.forEach(function(item, index) {
                item.querySelector('.step-number').innerHTML = '手順' + (index + 1);
            });
        }   
    });
    // steps delete
    steps.addEventListener('click', function(e) {
        if(e.target.classList.contains('step-delete') || e.target.closest('.step-delete')){
            e.target.closest('.step').remove();
            var items = steps.querySelectorAll('.step');
            items.forEach(function(item, index) {
                item.querySelector('.step-number').innerHTML = '手順' + (index + 1);
            });
        }
    });
    // steps add
    var addStep = document.getElementById('add-step');
    addStep.addEventListener('click', function() {
        var stepCount = steps.querySelectorAll('.step').length;
        var step = document.createElement('div');
        step.classList.add('step');
        step.classList.add('flex');
        step.classList.add('justify-between');
        step.classList.add('items-center');
        step.classList.add('mb-2');
        step.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" class="handle cursor-pointer"/>
            </svg>
            <p class="step-number w-20 text-center">手順${stepCount+1}</p>                  
            <input type="text" name="steps[]" class="border border-gray-300 p-2 w-full rounded" placeholder="手順を入力">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 ml-4 step-delete text-gray-500">
                <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
            </svg>
        `;
        steps.appendChild(step);
    });

    // ingredients sortable
    var ingredients = document.getElementById('ingredients');
    Sortable.create(ingredients, {
        animation: 150,
        handle: '.handle', 
        onEnd: function (evt) {
            var items = ingredients.querySelectorAll('.ingredient');
            items.forEach(function(item, index) {
                item.querySelector('.ingredient-name').name = `ingredients[${index}][name]`;
                item.querySelector('.ingredient-quantity').name = `ingredients[${index}][quantity]`;
            });
        }   
    });
    // ingredients delete
    ingredients.addEventListener('click', function(e) {
        if(e.target.classList.contains('ingredient-delete') || e.target.closest('.ingredient-delete')){
            e.target.closest('.ingredient').remove();
            var items = ingredients.querySelectorAll('.ingredient');
            items.forEach(function(item, index) {
                item.querySelector('.ingredient-name').name = `ingredients[${index}][name]`;
                item.querySelector('.ingredient-quantity').name = `ingredients[${index}][quantity]`;
            });
        }
    });
    // ingredients add
    var addIngredient = document.getElementById('add-ingredient');
    // console.log(addIngredient);
    addIngredient.addEventListener('click', function() {
        //console.log('click');
        var ingredientCount = ingredients.querySelectorAll('.ingredient').length;
        var ingredient = document.createElement('div');
        //console.log(ingredient);
        ingredient.classList.add('ingredient');
        ingredient.classList.add('flex');
        ingredient.classList.add('justify-between');
        ingredient.classList.add('items-center');
        ingredient.classList.add('mb-2');
        ingredient.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" class="handle cursor-pointer"/>
        </svg>
        <input type="text" name="ingredients[${ingredientCount}][name]" class="ingredient-name ml-4 border border-gray-300 p-2 w-1/2 rounded" placeholder="材料名">
        <p class="mx-2">:</p>
        <input type="text" name="ingredients[${ingredientCount}][quantity]" class="ingredient-quantity border border-gray-300 p-2 w-1/2 rounded" placeholder="分量">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 ml-4 ingredient-delete cursor-pointer text-gray-500">
            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
        </svg>`;
        ingredients.appendChild(ingredient);
    });

    // delete button
    var deleteButton = document.getElementById('delete');
    deleteButton.addEventListener('click', function(e) {
        if(!confirm('本当に削除しますか？')){
            e.preventDefault();
        }
    });
}// window.onload = function() {} end