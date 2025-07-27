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
            $table->foreignId('truck_id')->nullable()->constrained('trucks'); // شناسه کامیون (رابطه با جدول کامیون‌ها)
            $table->foreignId('company_id')->nullable()->constrained('companies'); // شناسه شرکت
            $table->foreignId('driver_id')->nullable()->constrained('drivers'); // شناسه راننده
            $table->foreignId('location')->nullable()->constrained('locations');
            $table->decimal('empty_weight', 8, 2)->nullable(); // وزن خالی کامیون
            $table->decimal('loaded_weight', 8, 2)->nullable(); // وزن بار کامیون
            $table->integer('step')->default(1); // وضعیت
            $table->date('entry_date'); // زمان ورود به کارخانه (تاریخ)
            $table->time('entry_time')->nullable(); // زمان ورود به کارخانه
            $table->time('exit_time')->nullable(); // زمان خروج از کارخانه
            $table->float('driver_star')->default(0); // میزان رضایت راننده
            $table->softDeletes();
            $table->timestamps();
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
