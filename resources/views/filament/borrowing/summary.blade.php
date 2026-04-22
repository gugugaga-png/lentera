<div class="space-y-2 text-sm">

    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
        <div>📅 Days: <b>{{ $days }}</b></div>
        <div>💰 Daily Price: <b>Rp {{ number_format($price, 0, ',', '.') }}</b></div>
        <div>📦 Quantity: <b>{{ $qty }}</b></div>
    </div>

    <div class="p-3 rounded-lg bg-green-50 dark:bg-gray-800">
        <div class="font-semibold">
            Total: Rp {{ number_format($total, 0, ',', '.') }}
        </div>
    </div>

</div>