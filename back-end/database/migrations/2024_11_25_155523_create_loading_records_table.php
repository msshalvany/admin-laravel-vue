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
            $table->foreignId('location_id')->constrained('locations'); // شناسه مکان بارگیری (رابطه با جدول مکان‌ها)
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('driver_id')->constrained('drivers');
            $table->decimal('empty_weight', 8, 2); // وزن خالی کامیون
            $table->decimal('loaded_weight', 8, 2)->nullable(); // وزن بار کامیون
            $table->string('status')->default('pending');
            $table->time('entry_time'); // زمان ورود به کارخانه
            $table->timestamp('exit_time')->nullable()->nullable(); // زمان خروج از کارخانه
            $table->float('driver_star')->default(0); // میزان رضایت راننده
            $table->softDeletes(); // حذف امن (soft delete)
            $table->timestamps(); // زمان‌های ثبت و بروزرسانی
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
