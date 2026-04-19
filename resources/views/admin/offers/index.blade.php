@extends('layouts.admin')

@section('title', __('Offers'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ __('Offers & bundles') }}</h2>
            <p class="text-gray-500 text-sm">{{ __('Combine products and set one bundle price') }}</p>
        </div>
        <a href="{{ route('admin.offers.create') }}" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('Add offer') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Products') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Bundle price') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($offers as $offer)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800">{{ $offer->name }}</span>
                                @if($offer->name_de)
                                    <span class="block text-sm text-gray-500">{{ $offer->name_de }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $offer->products_count }}</td>
                            <td class="px-4 py-3 font-semibold text-primary-600">{{ format_currency($offer->bundle_price) }}</td>
                            <td class="px-4 py-3">
                                <form action="{{ route('admin.offers.toggle', $offer) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm px-2 py-1 rounded-full {{ $offer->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $offer->is_active ? __('Active') : __('Inactive') }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.offers.edit', $offer) }}" class="text-primary-600 hover:text-primary-800 text-sm">{{ __('Edit') }}</a>
                                <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('Delete this offer?')));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('No offers yet. Create one to show bundles on the site.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($offers->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $offers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
