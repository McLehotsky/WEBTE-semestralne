@props(['icon' => '', 'title' => '', 'route' => '#'])

<a href="{{ $route }}"
   class="flex items-center gap-4 bg-white hover:bg-gray-100 border border-gray-200 p-5 rounded-xl shadow transition duration-200">
    
    {{-- Ikona inline (Heroicons) --}}
    <div class="w-8 h-8">
        {!! $icon !!}
    </div>

    <div class="text-left">
        <div class="text-lg font-semibold text-gray-800">{{ $title }}</div>
    </div>
</a>
