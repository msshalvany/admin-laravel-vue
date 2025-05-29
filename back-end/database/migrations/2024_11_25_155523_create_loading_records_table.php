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
        Schema::create('loading_records', function (Blueprint $table) {
            $table->id(); // شناسه بارگیری
            $table->foreignId('truck_id')->constrained('trucks'); // شناسه کامیون (رابطه با جدول کامیون‌ها)
            $table->foreignId('company_id')->constrained('companies'); // شناسه شرکت
            $table->foreignId('driver_id')->constrained('drivers'); // شناسه راننده
            $table->decimal('empty_weight', 8, 2); // وزن خالی کامیون
            $table->decimal('loaded_weight', 8, 2)->nullable(); // وزن بار کامیون
            $table->enum('status',['pending', 'ended'])->default('pending'); // وضعیت
            $table->date('entry_date'); // زمان ورود به کارخانه (تاریخ)
            $table->time('entry_time')->nullable(); // زمان ورود به کارخانه
            $table->time('exit_time')->nullable(); // زمان خروج از کارخانه
            $table->float('driver_star')->default(0); // میزان رضایت راننده
            $table->softDeletes(); // حذف امن (soft delete)
            $table->timestamps(); // ایجاد ستون‌های created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loading_records');
    }
};
