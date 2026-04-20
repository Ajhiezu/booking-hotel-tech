<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('address')->nullable()->after('avatar');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->boolean('is_active')->default(true)->after('gender');
            $table->string('verification_status')->default('unverified')->after('is_active'); // for hotel owners
            $table->text('bio')->nullable()->after('verification_status');
            $table->string('id_card_number')->nullable()->after('bio');
            $table->string('id_card_image')->nullable()->after('id_card_number');
            $table->timestamp('last_login_at')->nullable()->after('id_card_image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'avatar', 'address', 'date_of_birth', 'gender',
                'is_active', 'verification_status', 'bio',
                'id_card_number', 'id_card_image', 'last_login_at'
            ]);
        });
    }
};
