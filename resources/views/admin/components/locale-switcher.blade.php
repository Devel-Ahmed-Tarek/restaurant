<div class="flex rounded-lg border border-gray-200 overflow-hidden text-sm shadow-sm">
    <form method="POST" action="{{ route('admin.locale') }}" class="inline">@csrf
        <input type="hidden" name="locale" value="en">
        <button type="submit" class="px-3 py-1.5 font-medium transition-colors {{ app()->getLocale() === 'en' ? 'bg-primary-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">EN</button>
    </form>
    <form method="POST" action="{{ route('admin.locale') }}" class="inline border-l border-gray-200">@csrf
        <input type="hidden" name="locale" value="de">
        <button type="submit" class="px-3 py-1.5 font-medium transition-colors {{ app()->getLocale() === 'de' ? 'bg-primary-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">DE</button>
    </form>
</div>
