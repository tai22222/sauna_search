<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 一時的な外部キー制約を無効化
        Schema::disableForeignKeyConstraints();
    
        // prefecture_idカラムの型をbigintに変更
        Schema::table('saunas', function (Blueprint $table) {
            $table->unsignedBigInteger('prefecture_id')->change();
            $table->foreign('prefecture_id')->references('id')->on('prefectures');
        });
    
        // 一時的な外部キー制約を有効化
        Schema::enableForeignKeyConstraints();
    }
    
    public function down()
    {
        Schema::table('saunas', function (Blueprint $table) {
            $table->dropForeign(['prefecture_id']);
            $table->string('prefecture_id')->change();
        });
    }
};
