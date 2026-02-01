<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('email');
            $table->text('address')->nullable()->after('mobile');
            $table->enum('user_type', ['resident', 'non_resident'])->default('resident')->after('address');
            $table->boolean('verified')->default(false)->after('user_type');
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete()->after('verified');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['mobile', 'address', 'user_type', 'verified', 'role_id']);
        });
    }
};

