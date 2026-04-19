@extends('layouts.admin')

@section('title', $category ? __('Edit Category') : __('Create Category'))

@section('content')
<div class="max-w-2xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('Back to Categories') }}
        </a>
        <h2 class="text-xl font-bold text-gray-800">{{ $category ? __('Edit Category') : __('Create Category') }}</h2>
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
    <form action="{{ $category ? route('admin.categories.update', $category) : route('admin.categories.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
        @csrf
        @if($category)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Name (English)') }} *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category?->name) }}" required
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <!-- Name German -->
            <div>
                <label for="name_de" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Name (German)') }}</label>
                <input type="text" name="name_de" id="name_de" value="{{ old('name_de', $category?->name_de) }}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Icon -->
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Icon (Emoji)') }}</label>
                <input type="text" name="icon" id="icon" value="{{ old('icon', $category?->icon) }}" placeholder="e.g., 🍔"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <p class="text-xs text-gray-500 mt-1">{{ __('Enter an emoji icon') }}</p>
            </div>

            <!-- Sort Order -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Sort Order') }}</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        <!-- Image -->
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Image') }}</label>
            @if($category?->image)
                <div class="mb-3">
                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-24 h-24 rounded-lg object-cover">
                </div>
            @endif
            <input type="file" name="image" id="image" accept="image/*"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <!-- Status -->
        <div>
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}
                       class="w-5 h-5 text-primary-500 border-gray-300 rounded focus:ring-primary-500">
                <span class="text-sm font-medium text-gray-700">{{ __('Active') }}</span>
            </label>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors">
                {{ $category ? __('Update Category') : __('Create Category') }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
