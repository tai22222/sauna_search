<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaterBathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_baths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sauna_id');
            $table->unsignedBigInteger('bath_type_id');
            $table->unsignedBigInteger('water_type_id');
            $table->string('temperature');
            $table->integer('capacity');
            $table->string('deep_water');
            $table->text('additional_info')->nullable();
            $table->timestamps();
            $table->boolean('delete_flag')->default(0);

            // 外部キー制約
            $table->foreign('sauna_id')->references('id')->on('saunas');
            $table->foreign('bath_type_id')->references('id')->on('bath_types');
            $table->foreign('water_type_id')->references('id')->on('water_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('water_baths', function (Blueprint $table) {
            // 外部キー制約の削除
            $table->dropForeign(['sauna_id']);
            $table->dropForeign(['bath_type_id']);
            $table->dropForeign(['water_type_id']);
        });

        Schema::dropIfExists('water_baths');
    }
}
