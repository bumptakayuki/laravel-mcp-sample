<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->json('input');
            $table->string('output_summary');
            $table->string('executed_by')->default('unknown');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_audit_logs');
    }
};
