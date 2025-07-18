<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::create('locations', function (Blueprint $table) {
                $table->id(); // شناسه مکان
                $table->string('location_name'); // نام مکان
                $table->text('description')->nullable(); // توضیحات مکان
                $table->string('phone')->nullable(); //
                $table->ipAddress('ip')->nullable(); //
                $table->softDeletes(); // حذف امن (soft delete)
                $table->timestamps(); // زمان‌های ثبت و بروزرسانی
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
