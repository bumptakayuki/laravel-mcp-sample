<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcp_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tool_name');
            $table->json('input');
            $table->string('output_summary');
            $table->string('executed_by')->default('demo-user');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcp_audit_logs');
    }
};
