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
            $table->foreignId('truck_id')->constrained('trucks')->onDelete('cascade'); // شناسه کامیون (رابطه با جدول کامیون‌ها)
            $table->decimal('empty_weight', 8, 2); // وزن خالی کامیون
            $table->decimal('loaded_weight', 8, 2); // وزن بار کامیون
            $table->decimal('total_weight', 8, 2); // مجموع وزن کامیون + بار
            $table->decimal('customer_weight', 8, 2)->nullable(); // وزن اختصاصی مشتری
            $table->float('load_percentage'); // درصد بارگیری
            $table->timestamp('entry_time'); // زمان ورود به کارخانه
            $table->timestamp('exit_time')->nullable(); // زمان خروج از کارخانه
            $table->timestamp('loading_start_time'); // زمان شروع بارگیری
            $table->timestamp('loading_end_time'); // زمان پایان بارگیری
            $table->foreignId('location_id')->constrained('locations'); // شناسه مکان بارگیری (رابطه با جدول مکان‌ها)
            $table->integer('driver_satisfaction')->default(0)->check('driver_satisfaction >= 0 and driver_satisfaction <= 20'); // میزان رضایت راننده
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
