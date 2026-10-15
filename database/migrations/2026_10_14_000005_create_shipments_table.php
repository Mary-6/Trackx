<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 32)->unique();
            $table->foreignId('shipment_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('shipment_status_id')->constrained('shipment_statuses')->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->string('sender_name');
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('sender_address')->nullable();

            $table->string('recipient_name');
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_address')->nullable();

            $table->string('origin_country');
            $table->string('origin_city');
            $table->decimal('origin_latitude', 10, 7)->nullable();
            $table->decimal('origin_longitude', 10, 7)->nullable();

            $table->string('destination_country');
            $table->string('destination_city');
            $table->decimal('destination_latitude', 10, 7)->nullable();
            $table->decimal('destination_longitude', 10, 7)->nullable();

            $table->string('current_location_name')->nullable();
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->timestamp('current_location_updated_at')->nullable();

            $table->string('shipping_method')->nullable();
            $table->date('dispatch_date')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->string('customs_reference')->nullable();

            $table->text('public_notes')->nullable();
            $table->text('private_notes')->nullable();

            $table->boolean('is_archived')->default(false);
            $table->string('archive_reason')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('shipment_status_id');
            $table->index('created_at');
            $table->index('destination_country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
