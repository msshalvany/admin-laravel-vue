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
            $table->string('plate_number'); // پلاک کامیون
            $table->decimal('weight', 8, 2); // وزن کامیون
            $table->enum('status', ['active', 'inactive']); // وضعیت کامیون
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
