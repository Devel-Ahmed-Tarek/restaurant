@extends('layouts.admin')

@section('title', $offer ? __('Edit offer') : __('Create offer'))

@section('content')
<div class="max-w-4xl" x-data="offerForm()">
    <div class="mb-6">
        <a href="{{ route('admin.offers.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('Back to offers') }}
        </a>
        <h2 class="text-xl font-bold text-gray-800">{{ $offer ? __('Edit offer') : __('Create offer') }}</h2>
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

    <form action="{{ $offer ? route('admin.offers.update', $offer) : route('admin.offers.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @if($offer)
            @method('PUT')
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Offer details') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Name (English)') }} *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $offer?->name) }}" required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="name_de" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Name (German)') }}</label>
                    <input type="text" name="name_de" id="name_de" value="{{ old('name_de', $offer?->name_de) }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="2"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $offer?->description) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label for="description_de" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description (German)') }}</label>
                    <textarea name="description_de" id="description_de" rows="2"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description_de', $offer?->description_de) }}</textarea>
                </div>
                <div>
                    <label for="bundle_price" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Bundle price (:currency)', ['currency' => currency_symbol()]) }} *</label>
                    <input type="number" name="bundle_price" id="bundle_price" step="0.01" min="0" required
                           value="{{ old('bundle_price', $offer?->bundle_price) }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Sort order') }}</label>
                    <input type="number" name="sort_order" id="sort_order" min="0"
                           value="{{ old('sort_order', $offer?->sort_order ?? 0) }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Image') }}</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full text-sm text-gray-600">
                    @if($offer?->image)
                        <p class="text-xs text-gray-500 mt-1">{{ __('Current image will be kept unless you upload a new one.') }}</p>
                    @endif
                </div>
                <div class="md:col-span-2 flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                           {{ old('is_active', $offer?->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm text-gray-700">{{ __('Active (visible on site)') }}</label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">{{ __('Products in this bundle') }}</h3>
                <button type="button" @click="addLine()" class="text-sm text-primary-600 hover:text-primary-800 font-medium">+ {{ __('Add product') }}</button>
            </div>
            <p class="text-sm text-gray-500 mb-4">{{ __('Add each meal/item and how many of each. The customer pays only the bundle price above.') }}</p>

            <div class="space-y-3">
                <template x-for="(line, index) in lines" :key="index">
                    <div class="flex flex-wrap gap-3 items-end p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('Product') }}</label>
                            <select :name="'lines[' + index + '][product_id]'" x-model="line.product_id" required
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500">
                                <option value="">{{ __('Select product') }}</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-24">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('Qty') }}</label>
                            <input type="number" :name="'lines[' + index + '][quantity]'" x-model.number="line.quantity" min="1" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <button type="button" @click="removeLine(index)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="{{ __('Remove') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.offers.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">{{ __('Cancel') }}</a>
            <button type="submit" class="px-6 py-2 rounded-lg bg-primary-500 hover:bg-primary-600 text-white font-medium">
                {{ $offer ? __('Update offer') : __('Create offer') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function offerForm() {
    return {
        lines: @json($initialLines),
        addLine() {
            this.lines.push({ product_id: '', quantity: 1 });
        },
        removeLine(index) {
            if (this.lines.length > 1) {
                this.lines.splice(index, 1);
            }
        }
    };
}
</script>
@endpush
@endsection
