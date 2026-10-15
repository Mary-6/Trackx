<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Core listing fields
            $table->string('title')->nullable()->after('customer_id');
            $table->string('product_name')->nullable()->after('title');
            $table->unsignedSmallInteger('quantity')->nullable()->after('weight_kg');
            $table->string('status_reason')->nullable()->after('customs_reference');

            // Granular party details
            $table->string('sender_house_number', 30)->nullable()->after('sender_address');
            $table->string('sender_city', 80)->nullable()->after('sender_house_number');
            $table->string('sender_state', 80)->nullable()->after('sender_city');
            $table->string('sender_country', 80)->nullable()->after('sender_state');
            $table->string('recipient_house_number', 30)->nullable()->after('recipient_address');
            $table->string('recipient_city', 80)->nullable()->after('recipient_house_number');
            $table->string('recipient_state', 80)->nullable()->after('recipient_city');
            $table->string('recipient_country', 80)->nullable()->after('recipient_state');

            // Optional times alongside the existing dates
            $table->time('departure_time')->nullable()->after('dispatch_date');
            $table->time('delivery_time')->nullable()->after('estimated_delivery_date');
        });

        // Only tracking number, category, status and a title/description stay mandatory.
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('sender_name')->nullable()->change();
            $table->string('recipient_name')->nullable()->change();
            $table->string('origin_country')->nullable()->change();
            $table->string('origin_city')->nullable()->change();
            $table->string('destination_country')->nullable()->change();
            $table->string('destination_city')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'product_name', 'quantity', 'status_reason',
                'sender_house_number', 'sender_city', 'sender_state', 'sender_country',
                'recipient_house_number', 'recipient_city', 'recipient_state', 'recipient_country',
                'departure_time', 'delivery_time',
            ]);
        });
    }
};
