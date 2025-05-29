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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id(); // شناسه کامیون
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade'); // شناسه راننده
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            $table->string('plate_right', 2);  // مثلاً 12
            $table->string('plate_char', 1);   // مثلاً الف
            $table->string('plate_left', 3);   // مثلاً 345
            $table->string('plate_city', 2);   // مثلاً 51
            // پلاک کامل برای راحتی در سرچ و نمایش;      // مثلاً ۱۲الف۳۴۵ - ۵۱
            $table->string('plate_full');
            $table->string('color'); // پلاک کامیون
            $table->enum('type', ['غیره','کامیون', 'تریلی', 'کامیونت', 'خاور', 'وانت'])->nullable(); // نوع وسیله نقلیه
            $table->decimal('weight', 8, 2); // وزن کامیون
            $table->softDeletes(); // حذف امن (soft delete)
            $table->timestamps(); // زمان‌های ثبت و بروزرسانی
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
