<x-filament-panels::page>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                animation: {
                    'float': 'float 3s ease-in-out infinite',
                    'pulse-soft': 'pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                },
                keyframes: {
                    float: {
                        '0%, 100%': { transform: 'translateY(0px)' },
                        '50%': { transform: 'translateY(-5px)' },
                    },
                    'pulse-soft': {
                        '0%, 100%': { opacity: '1' },
                        '50%': { opacity: '0.8' },
                    }
                }
            }
        }
    }
</script>

<div class="flex flex-col gap-8">

    {{-- Hero Banner - Enhanced --}}
    <div class="relative overflow-hidden rounded-2xl p-8 text-dark shadow-xl" 
         style="background: linear-gradient(135deg, rgb(var(--primary-700)) 0%, rgb(var(--primary-500)) 40%, rgb(var(--primary-400)) 100%)">
        
        {{-- Decorative Elements --}}
        <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full bg-white opacity-[0.08] blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/4 w-96 h-96 rounded-full bg-white opacity-[0.06] blur-3xl"></div>
        <div class="absolute top-1/2 right-1/3 w-48 h-48 rounded-full bg-white opacity-[0.04] blur-2xl"></div>
        
        {{-- Animated Pattern --}}
        <svg class="absolute inset-0 w-full h-full opacity-5" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center animate-float">
                    <span class="text-2xl">👋</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="text-sm mt-1 opacity-90 font-medium">Manage your borrowings and explore available items below.</p>
                </div>
            </div>

            {{-- Stats Cards - Enhanced --}}
            <div class="flex flex-wrap gap-4 mt-6">
                @foreach([
                    ['value' => $items->count(), 'label' => 'Available Items', 'icon' => '📦', 'color' => 'from-emerald-500/20 to-emerald-500/5'],
                    ['value' => $borrowings->where('status','approved')->count(), 'label' => 'Active Borrowings', 'icon' => '📚', 'color' => 'from-blue-500/20 to-blue-500/5'],
                    ['value' => $borrowings->where('status','pending')->count(), 'label' => 'Pending Requests', 'icon' => '⏳', 'color' => 'from-amber-500/20 to-amber-500/5'],
                    ['value' => $borrowings->where('status','returned')->count(), 'label' => 'Returned Items', 'icon' => '✅', 'color' => 'from-purple-500/20 to-purple-500/5'],
                ] as $index => $stat)
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-br {{ $stat['color'] }} rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-white/15 backdrop-blur-md rounded-2xl px-6 py-4 min-w-[110px] border border-white/20 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">{{ $stat['icon'] }}</span>
                            <div class="text-2xl font-bold leading-none">{{ $stat['value'] }}</div>
                        </div>
                        <div class="text-xs font-semibold uppercase tracking-wider opacity-90">{{ $stat['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Main Grid - Enhanced Spacing --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-8 ">

        {{-- LEFT: Items Section - Enhanced --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-6 rounded-full bg-gradient-to-b from-primary-500 to-primary-600"></div>
                <span class="text-sm font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">🛠 Available Items</span>
                <div class="flex-1 h-px bg-gradient-to-r from-gray-300 to-transparent dark:from-gray-600"></div>
                <span class="text-xs text-gray-400 font-medium">{{ $items->count() }} items</span>
            </div>

            <div class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-5">
                @forelse($items as $item)
                <div id="card-{{ $item->id }}"
                    class="relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1.5 cursor-pointer group border border-gray-200 dark:border-gray-700"
                    style="box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);">

                    {{-- Hover Effect Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-500/0 via-transparent to-primary-500/0 group-hover:from-primary-500/5 group-hover:to-primary-500/5 transition-all duration-500"></div>

                    {{-- Condition Badge - Enhanced --}}
                    @if($item->condition)
                    <span class="absolute top-3 left-3 z-10 text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full shadow-md backdrop-blur-sm
                        {{ $item->condition === 'good' ? 'bg-green-500/90 text-white' : ($item->condition === 'damaged' ? 'bg-red-500/90 text-white' : 'bg-yellow-500/90 text-white') }}">
                        {{ match($item->condition) { 'good'=>'✨ Good','damaged'=>'⚠️ Damaged','maintenance'=>'🔧 Maintenance',default=>$item->condition } }}
                    </span>
                    @endif

                    {{-- Photo Section - Enhanced --}}
                    <div class="relative overflow-hidden bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800">
                        @if($item->photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::temporaryUrl($item->photo, now()->addHours(2)) }}"
                                class="w-full h-36 object-cover transition-transform duration-500 group-hover:scale-110" alt="{{ $item->name }}">
                        @else
                            <div class="w-full h-36 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif
                        
                        {{-- Gradient Overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>

                    <div class="p-4">
                        @if($item->code)
                        <span class="text-[10px] font-mono font-semibold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 px-2 py-1 rounded-md inline-block mb-2 tracking-wider border border-primary-200 dark:border-primary-800">
                            {{ $item->code }}
                        </span>
                        @endif
                        
                        <p class="font-bold text-base text-gray-900 dark:text-gray-100 leading-tight mb-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                            {{ $item->name }}
                        </p>
                        
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ $item->category->name ?? 'Uncategorized' }}
                        </p>

                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <span class="text-xl font-bold  text-gray-400  bg-clip-text">
                                    Rp {{ number_format($item->daily_rental_price, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-medium text-gray-400 ml-0.5">/day</span>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm
                                {{ $item->available_stock > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300 border border-green-300 dark:border-green-700' : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300 border border-red-300 dark:border-red-700' }}">
                                {{ $item->available_stock > 0 ? $item->available_stock.' in stock' : 'Out of stock' }}
                            </span>
                        </div>

                        @if($item->available_stock > 0)
                        <button
                            onclick="selectItem({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->daily_rental_price }}, {{ $item->available_stock }}, '{{ $item->code ?? '' }}')"
                            class="btn-select-{{ $item->id }} group/btn relative w-full py-2.5 px-4 text-sm font-semibold rounded-xl transition-all duration-300 overflow-hidden"
                            style="background: linear-gradient(135deg, rgb(var(--primary-500)) 0%, rgb(var(--primary-600)) 100%);">
                            
                            {{-- Button Shine Effect --}}
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700"></div>
                            
                            <span class="relative flex items-center justify-center gap-2 text-gray-800 dark:text-white group-hover/btn:text-blue-500 transition-colors duration-300">
    <svg class="w-4 h-4 transition-all duration-300 group-hover/btn:rotate-90 stroke-current" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
    </svg>
    <span>Select Item</span>
</span>
                        </button>
                        @else
                        <div class="w-full py-2.5 px-4 text-sm font-semibold rounded-xl text-center bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed">
                            Currently Unavailable
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No items available at the moment.</p>
                        <p class="text-sm text-gray-400 mt-1">Check back later for new items!</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT: Forms - Enhanced --}}
        <div class="flex flex-col gap-5">

            {{-- Borrow Form - Enhanced --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-primary-50/50 to-transparent dark:from-primary-900/20">
                    <div class="w-8 h-8 rounded-lg bg-primary-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <p class="text-base font-bold text-gray-900 dark:text-gray-100">Borrow Item</p>
                </div>

                <div class="p-6">
                    <form wire:submit.prevent="submitBorrowing" class="flex flex-col gap-4">
                        <input type="hidden" id="item_id" wire:model="selectedItem">

                        {{-- Preview - Enhanced --}}
                        <div id="preview-empty" class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center">
                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Select an item from the list</p>
                            <p class="text-xs text-gray-400 mt-1">Click on any available item to begin</p>
                        </div>

                        <div id="preview-filled" class="rounded-xl p-4 hidden transition-all duration-300"
                             style="background: linear-gradient(135deg, rgba(var(--primary-500), 0.1) 0%, rgba(var(--primary-500), 0.05) 100%);
                                    border: 1.5px solid rgba(var(--primary-500), 0.3);">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-lg bg-primary-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p id="preview-name" class="text-base font-bold text-primary-700 dark:text-primary-300">-</p>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Code</span>
                                    <strong id="preview-code" class="text-gray-900 dark:text-gray-100 font-mono">-</strong>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Daily Price</span>
                                    <strong id="preview-price" class="text-gray-900 dark:text-gray-100">-</strong>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Available Stock</span>
                                    <strong id="preview-stock" class="text-gray-900 dark:text-gray-100">-</strong>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Quantity</label>
                            <div class="relative">
                                <input type="number" wire:model="quantity" min="1" placeholder="Enter quantity"
                                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-transparent transition-all"
                                    style="--tw-ring-color:rgb(var(--primary-500))">
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                            </div>
                            @error('quantity') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><span>⚠️</span>{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Borrow Date</label>
                                <input type="date" wire:model="borrow_date" wire:change="calculateTotalCost" min="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-transparent">
                                @error('borrow_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Return Date</label>
                                <input type="date" wire:model="estimated_return_date" wire:change="calculateTotalCost" min="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-transparent">
                                @error('estimated_return_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        @if($total_cost > 0)
                        <div class="flex justify-between items-center rounded-xl px-4 py-3 animate-pulse-soft"
                             style="background: linear-gradient(135deg, rgba(var(--primary-500), 0.15) 0%, rgba(var(--primary-500), 0.08) 100%);
                                    border: 1.5px solid rgba(var(--primary-500), 0.3);">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Total</span>
                            <span class="text-xl font-bold text-primary-700 dark:text-primary-300">Rp {{ number_format($total_cost, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <button type="submit" 
                                class="w-full py-3 text-sm font-bold rounded-xl text-white transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                                style="background: linear-gradient(135deg, rgb(var(--primary-600)) 0%, rgb(var(--primary-700)) 100%);">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Submit Borrowing Request
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Return Form - Enhanced --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-yellow-50/50 to-transparent dark:from-yellow-900/20">
                    <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                    <p class="text-base font-bold text-gray-900 dark:text-gray-100">Return Item</p>
                </div>

                <div class="p-6">
                    <form wire:submit.prevent="submitReturn" class="flex flex-col gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Select Borrowing</label>
                            <select wire:model="return_borrowing_id" wire:change="calculateReturnFine"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                <option value="">Choose active borrowing...</option>
                                @foreach($borrowings as $borrowing)
                                    @if($borrowing->status === 'approved')
                                    <option value="{{ $borrowing->id }}">
                                        {{ $borrowing->item->name }}{{ $borrowing->item->code ? ' ('.$borrowing->item->code.')' : '' }}
                                        — {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Return Date</label>
                            <input type="date" wire:model="return_date" wire:change="calculateReturnFine"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        </div>

                        @if($return_fine > 0)
                        <div class="flex justify-between items-center rounded-xl px-4 py-3 bg-gradient-to-r from-red-50 to-red-100/50 dark:from-red-900/20 dark:to-red-800/10 border border-red-200 dark:border-red-800 animate-pulse-soft">
                            <span class="text-sm font-medium text-red-700 dark:text-red-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Late Return Fine
                            </span>
                            <span class="text-xl font-bold text-red-700 dark:text-red-400">Rp {{ number_format($return_fine, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <button type="submit" 
                                class="w-full py-3 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Confirm Return
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- My Borrowings - Enhanced --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-transparent dark:from-gray-900/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                        </div>
                        <p class="text-base font-bold text-gray-900 dark:text-gray-100">My Borrowings</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                        {{ $borrowings->count() }} total
                    </span>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-80 overflow-y-auto">
                    @forelse($borrowings as $borrowing)
                    <div class="group px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-200">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        {{ $borrowing->item->name }}
                                    </p>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                                    @if($borrowing->item->code)
                                    <span class="font-mono font-semibold bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-md text-gray-700 dark:text-gray-300">
                                        {{ $borrowing->item->code }}
                                    </span>
                                    @endif
                                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 12h6M9 16h6"/>
                                        </svg>
                                        Qty: {{ $borrowing->quantity }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                                    </span>
                                </div>

                                @if($borrowing->estimated_return_date)
                                <div class="mt-2 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Due: {{ \Carbon\Carbon::parse($borrowing->estimated_return_date)->format('d M Y') }}
                                    </p>
                                </div>
                                @endif
                            </div>

                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full shrink-0 shadow-sm
                                {{ $borrowing->status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-700' :
                                   ($borrowing->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300 border border-green-300 dark:border-green-700' :
                                   ($borrowing->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300 border border-red-300 dark:border-red-700' :
                                   'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border border-gray-300 dark:border-gray-600')) }}">
                                {{ ucfirst($borrowing->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No borrowing history yet</p>
                        <p class="text-sm text-gray-400 mt-1">Your borrowed items will appear here</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Keep the original JavaScript unchanged --}}
<script>
function selectItem(itemId, itemName, dailyPrice, maxStock, itemCode) {
    document.getElementById('item_id').value = itemId;
    document.getElementById('item_id').dispatchEvent(new Event('input'));

    document.getElementById('preview-empty').classList.add('hidden');
    document.getElementById('preview-filled').classList.remove('hidden');
    document.getElementById('preview-name').innerText = itemName;
    document.getElementById('preview-code').innerText = itemCode || '-';
    document.getElementById('preview-price').innerText = 'Rp ' + Number(dailyPrice).toLocaleString('id-ID') + '/day';
    document.getElementById('preview-stock').innerText = maxStock;

    document.querySelectorAll('[id^="card-"]').forEach(c => c.classList.remove('ring-2', 'ring-amber-400'));
    document.getElementById('card-' + itemId)?.classList.add('ring-2', 'ring-amber-400');

    document.querySelectorAll('[class*="btn-select-"]').forEach(b => b.innerText = 'Select to Borrow');
    const btn = document.querySelector('.btn-select-' + itemId);
    if (btn) { 
        const span = btn.querySelector('span span') || btn.querySelector('span');
        if (span) span.innerText = 'Selected';
        btn.style.background = 'linear-gradient(135deg, rgb(var(--primary-700)) 0%, rgb(var(--primary-800)) 100%)';
    }
}
</script>

</x-filament-panels::page>