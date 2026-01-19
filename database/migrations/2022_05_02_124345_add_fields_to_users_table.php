<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->smallInteger('level')->default(1)->after('profile_photo_path');
            $table->json('filters')->nullable()->after('level');
            $table->smallInteger('active')->default(1)->after('filters');
            $table->timestamp('last_login')->nullable()->after('active');
            $table->tinyInteger('need_change_password')->default(0)->after('last_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('level', 'filters', 'active', 'last_login', 'need_change_password');
        });
    }
}
