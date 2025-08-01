<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
{
    Schema::create('advertisements', function (Blueprint $table) {
        $table->id();
        $table->text('services');
        $table->integer('quantity');
        $table->decimal('price', 10, 2)->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
