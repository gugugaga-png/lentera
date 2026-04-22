@php
    use Carbon\Carbon;
@endphp

@if(!isset($borrowing))
    <div class="text-red-500 p-4">
        Borrowing data not found
    </div>
@else

<div class="space-y-6 p-2">

    {{-- Item Info --}}
    <div class="flex items-start gap-4">
        @if($borrowing->item?->photo)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($borrowing->item->photo) }}" 
                 class="w-20 h-20 rounded-lg object-cover"
                 alt="{{ $borrowing->item->name }}">
        @else
            <div class="w-20 h-20 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <x-filament::icon icon="heroicon-o-cube" class="w-10 h-10 text-gray-400" />
            </div>
        @endif

        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                {{ $borrowing->item->name ?? '-' }}
            </h3>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Code: <span class="font-mono">{{ $borrowing->item->code ?? '-' }}</span>
            </p>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Category: {{ $borrowing->item->category->name ?? 'Uncategorized' }}
            </p>
        </div>
    </div>

</div>

@endif