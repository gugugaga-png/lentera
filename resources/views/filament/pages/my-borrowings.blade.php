<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $stats = [
                    [
                        'label' => 'Pending',
                        'value' => \App\Models\Borrowing::where('user_id', auth()->id())->where('status', 'pending')->count(),
                        'icon' => 'heroicon-o-clock',
                        'color' => 'warning',
                    ],
                    [
                        'label' => 'Approved',
                        'value' => \App\Models\Borrowing::where('user_id', auth()->id())->where('status', 'approved')->count(),
                        'icon' => 'heroicon-o-check-circle',
                        'color' => 'success',
                    ],
                    [
                        'label' => 'Returned',
                        'value' => \App\Models\Borrowing::where('user_id', auth()->id())->where('status', 'returned')->count(),
                        'icon' => 'heroicon-o-archive-box',
                        'color' => 'gray',
                    ],
                ];
            @endphp
            
            @foreach($stats as $stat)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stat['value'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 flex items-center justify-center">
                        <x-filament::icon
                            :icon="$stat['icon']"
                            class="w-6 h-6 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"
                        />
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Table Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>