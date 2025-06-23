<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('building_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('building_services');
    }
};
