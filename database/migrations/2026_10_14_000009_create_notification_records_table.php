<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('channel')->default('mail');
            $table->string('recipient');
            $table->string('status')->default('pending'); // pending | sent | failed | skipped
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_records');
    }
};
