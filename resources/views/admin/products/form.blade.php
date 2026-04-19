@extends('layouts.admin')

@section('title', $product ? __('Edit Product') : __('Create Product'))

@section('content')
<div class="max-w-4xl" x-data="productForm()">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('Back to Products') }}
        </a>
        <h2 class="text-xl font-bold text-gray-800">{{ $product ? __('Edit Product') : __('Create Product') }}</h2>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @if($product)
            @method('PUT')
        @endif

        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Basic Information') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div class="md:col-span-2">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Category') }} *</label>
                    <select name="category_id" id="category_id" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">{{ __('Select Category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product?->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Name (English)') }} *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product?->name) }}" required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Name German -->
                <div>
                    <label for="name_de" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Name (German)') }}</label>
                    <input type="text" name="name_de" id="name_de" value="{{ old('name_de', $product?->name_de) }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $product?->description) }}</textarea>
                </div>

                <!-- Description German -->
                <div>
                    <label for="description_de" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description (German)') }}</label>
                    <textarea name="description_de" id="description_de" rows="3"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description_de', $product?->description_de) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Pricing') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Base Price -->
                <div>
                    <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Base Price (:currency)', ['currency' => currency_symbol()]) }} *</label>
                    <input type="number" name="base_price" id="base_price" value="{{ old('base_price', $product?->base_price) }}" step="0.01" min="0" required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Old Price -->
                <div>
                    <label for="old_price" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Old Price (for discounts)') }}</label>
                    <input type="number" name="old_price" id="old_price" value="{{ old('old_price', $product?->old_price) }}" step="0.01" min="0"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Product Image') }}</h3>
            
            @if($product?->image)
                <div class="mb-4">
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 rounded-lg object-cover">
                </div>
            @endif
            
            <input type="file" name="image" id="image" accept="image/*"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <!-- Sizes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">{{ __('Sizes') }}</h3>
                <button type="button" @click="addSize()" class="text-primary-500 hover:text-primary-600 text-sm">+ {{ __('Add Size') }}</button>
            </div>
            
            <div class="space-y-3">
                <template x-for="(size, index) in sizes" :key="index">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <input type="text" :name="'sizes['+index+'][name]'" x-model="size.name" placeholder="{{ __('Size name (e.g., Small)') }}" required
                               class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <input type="text" :name="'sizes['+index+'][name_de]'" x-model="size.name_de" placeholder="{{ __('German name') }}"
                               class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <input type="number" :name="'sizes['+index+'][price_modifier]'" x-model="size.price_modifier" placeholder="{{ __('+/- Price') }}" step="0.01" required
                               class="w-32 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <button type="button" @click="removeSize(index)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
            
            <p x-show="sizes.length === 0" class="text-gray-500 text-sm">{{ __('No sizes added. Click "Add Size" to add product sizes.') }}</p>
        </div>

        <!-- Toppings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">{{ __('Toppings / Add-ons') }}</h3>
                <button type="button" @click="addTopping()" class="text-primary-500 hover:text-primary-600 text-sm">+ {{ __('Add Topping') }}</button>
            </div>
            
            <div class="space-y-3">
                <template x-for="(topping, index) in toppings" :key="index">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <input type="text" :name="'toppings['+index+'][name]'" x-model="topping.name" placeholder="{{ __('Topping name') }}" required
                               class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <input type="text" :name="'toppings['+index+'][name_de]'" x-model="topping.name_de" placeholder="{{ __('German name') }}"
                               class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <input type="number" :name="'toppings['+index+'][price]'" x-model="topping.price" placeholder="{{ __('Price') }}" step="0.01" min="0" required
                               class="w-24 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <label class="flex items-center gap-1 text-sm text-gray-600">
                            <input type="checkbox" :name="'toppings['+index+'][is_required]'" x-model="topping.is_required" value="1" class="rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                            {{ __('Required') }}
                        </label>
                        <button type="button" @click="removeTopping(index)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
            
            <p x-show="toppings.length === 0" class="text-gray-500 text-sm">{{ __('No toppings added. Click "Add Topping" to add product toppings.') }}</p>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Status') }}</h3>
            
            <div class="space-y-3">
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product?->is_available ?? true) ? 'checked' : '' }}
                           class="w-5 h-5 text-primary-500 border-gray-300 rounded focus:ring-primary-500">
                    <span class="text-sm font-medium text-gray-700">{{ __('Available for ordering') }}</span>
                </label>
                
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product?->is_featured) ? 'checked' : '' }}
                           class="w-5 h-5 text-primary-500 border-gray-300 rounded focus:ring-primary-500">
                    <span class="text-sm font-medium text-gray-700">{{ __('Featured product (show on home page)') }}</span>
                </label>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                {{ $product ? __('Update Product') : __('Create Product') }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function productForm() {
    return {
        sizes: @json(old('sizes', $initialSizes ?? [])),
        toppings: @json(old('toppings', $initialToppings ?? [])),
        
        addSize() {
            this.sizes.push({ name: '', name_de: '', price_modifier: 0 });
        },
        
        removeSize(index) {
            this.sizes.splice(index, 1);
        },
        
        addTopping() {
            this.toppings.push({ name: '', name_de: '', price: 0, is_required: false });
        },
        
        removeTopping(index) {
            this.toppings.splice(index, 1);
        }
    }
}
</script>
@endpush
@endsection
