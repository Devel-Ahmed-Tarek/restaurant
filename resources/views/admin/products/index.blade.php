@extends('layouts.admin')

@section('title', __('Products'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Products') }}</h2>
            <p class="text-gray-500 text-sm">{{ __('Manage your menu items') }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('Add Product') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search products...') }}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="w-48">
                <select name="category" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                {{ __('Filter') }}
            </button>
            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Image -->
                <div class="relative aspect-square bg-gray-100">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl">🍽️</div>
                    @endif
                    
                    <!-- Status Badge -->
                    @if(!$product->is_available)
                        <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ __('Unavailable') }}
                        </div>
                    @endif
                    
                    @if($product->is_featured)
                        <div class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ __('Featured') }}
                        </div>
                    @endif
                </div>
                
                <!-- Info -->
                <div class="p-4">
                    <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
                    <h3 class="font-semibold text-gray-800 mt-1">{{ $product->name }}</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-primary-500 font-bold">{{ format_currency($product->base_price) }}</span>
                        @if($product->old_price)
                            <span class="text-gray-400 text-sm line-through">{{ format_currency($product->old_price) }}</span>
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.products.edit', $product) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm transition-colors">
                            {{ __('Edit') }}
                        </a>
                        <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full text-center {{ $product->is_available ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }} px-3 py-2 rounded-lg text-sm transition-colors">
                                {{ $product->is_available ? __('Disable') : __('Enable') }}
                            </button>
                        </form>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm(@json(__('Are you sure?')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <p>{{ __('No products found') }}</p>
                <a href="{{ route('admin.products.create') }}" class="text-primary-500 hover:text-primary-600 mt-2 inline-block">{{ __('Create your first product') }}</a>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
