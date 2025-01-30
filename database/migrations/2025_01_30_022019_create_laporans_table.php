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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); //menambahkan kolom user_id
            $table->string('name');
            $table->string('description');
            $table->string('location');
            $table->date('date');
            $table->enum('time', ['pagi', 'siang', 'sore', 'invalid']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('presence', ['hadir', 'sakit', 'izin', 'alpa']);
            $table->timestamps();

            // menambahkan kolom user_id yang diambil dari table users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            if (Schema::hasColumn('laporans', 'user_id')) {
                $table->dropForeign(['user_id']); //menghapus foreign key jika ada
            }
        });
        Schema::dropIfExists('laporans');
    }
};
