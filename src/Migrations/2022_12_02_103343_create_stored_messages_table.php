<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        try {
            DB::beginTransaction();

            Schema::create('stored_messages', function (Blueprint $table): void {
                $table->id();
                $table->string('message_id')->unique();
                $table->string('causation_id');
                $table->string('correlation_id');
                $table->string('name');
                $table->longText('content');
                $table->unsignedBigInteger('timestamp');
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['message_id', 'causation_id', 'correlation_id', 'deleted_at']);
            });

            Schema::table('stored_messages', function (Blueprint $table) {
                $table->foreign('causation_id')->references('message_id')->on('stored_messages')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreign('correlation_id')->references('message_id')->on('stored_messages')->cascadeOnUpdate()->restrictOnDelete();
            });

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            DB::beginTransaction();

            Schema::dropIfExists('stored_messages');

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
    }
};
