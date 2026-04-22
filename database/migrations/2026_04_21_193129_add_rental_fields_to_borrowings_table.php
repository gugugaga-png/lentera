<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->date('estimated_return_date')->nullable()->after('borrow_date');
            $table->integer('total_rental_cost')->default(0)->after('estimated_return_date');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['estimated_return_date', 'total_rental_cost']);
        });
    }
};