<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityTable extends Migration
{
    public function up()
    {
        Schema::create('facility', function (Blueprint $table) {
            $table->id()->unique()->nullable(false);
            $table->char('facility', 20)->nullable(false);

            // ← groupno は削除（持たない）

            $table->char('pass', 2)->nullable()->default(null);
            $table->char('address', 100)->nullable();
            $table->char('tel', 16)->nullable();
            $table->char('mail', 40)->nullable();

            $table->integer('item1')->default(0);
            $table->integer('item2')->default(0);
            $table->integer('item3')->default(0);
            $table->integer('item4')->default(0);
            $table->integer('item5')->default(0);
            $table->integer('item6')->default(0);
            $table->integer('item7')->default(0);
            $table->integer('item8')->default(0);
            $table->integer('item9')->default(0);
            $table->integer('item10')->default(0);
            $table->integer('item11')->default(0);
            $table->integer('item12')->default(0);
            $table->integer('item13')->default(0);
            $table->integer('item14')->default(0);
            $table->integer('item15')->default(0);
            $table->integer('item16')->default(0);
            $table->float('item17')->default(0);
            $table->integer('item18')->default(0);
            $table->integer('item19')->default(0);
            $table->integer('item20')->default(0);
            $table->float('item21')->default(0);
            $table->integer('item22')->default(0);
            $table->integer('item23')->default(0);
            $table->integer('item24')->default(0);
            $table->float('item25')->default(0);
            $table->integer('item26')->default(0);
            $table->integer('item27')->default(0);
            $table->integer('item28')->default(0);
            $table->float('item29')->default(0);
            $table->integer('item30')->default(0);
            $table->integer('item31')->default(0);
            $table->integer('item32')->default(0);
            $table->integer('item33')->default(0);
            $table->integer('item34')->default(0);
            $table->integer('item35')->default(0);
            $table->integer('item36')->default(0);
            $table->integer('item37')->default(0);
            $table->integer('item38')->default(0);
            $table->integer('item39')->default(0);
            $table->float('item40')->default(0);
            $table->integer('item41')->default(0);
            $table->integer('item42')->default(0);
            $table->float('item43')->default(0);
            $table->integer('item44')->default(0);
            $table->integer('item45')->default(0);
            $table->float('item46')->default(0);
            $table->integer('item47')->default(0);
            $table->float('item48')->default(0);
            $table->integer('item49')->default(0);
            $table->integer('item50')->default(0);
            $table->integer('item51')->default(0);
            $table->integer('item52')->default(0);
            $table->integer('item53')->default(0);
            $table->integer('item54')->default(0);
            $table->integer('item55')->default(0);
            $table->integer('item56')->default(0);
            $table->integer('item57')->default(0);
            $table->integer('item58')->default(0);
            $table->integer('item59')->default(0);
            $table->integer('item60')->default(0);
            $table->integer('item61')->default(0);
            $table->integer('item62')->default(0);
            $table->integer('item63')->default(0);
            $table->integer('item64')->default(0);
            $table->integer('item65')->default(0);
            $table->float('item66')->default(0);
            $table->integer('item67')->default(0);

            $table->char('currentfile', 50)->nullable()->default(null);
            $table->char('introfile', 50)->nullable()->default(null);
            $table->char('delflag', 1)->default('0');

            $table->char('pic1', 1)->nullable()->default(null);
            $table->char('pic2', 1)->nullable()->default(null);
            $table->char('pic3', 1)->nullable()->default(null);
            $table->char('pic4', 1)->nullable()->default(null);
            $table->char('pic5', 1)->nullable()->default(null);
            $table->char('pic6', 1)->nullable()->default(null);
            $table->char('pic7', 1)->nullable()->default(null);
            $table->char('pic8', 1)->nullable()->default(null);
            $table->char('pic9', 1)->nullable()->default(null);
            $table->char('pic10', 1)->nullable()->default(null);
            $table->char('pic11', 1)->nullable()->default(null);
            $table->char('pic12', 1)->nullable()->default(null);
            $table->char('pic13', 1)->nullable()->default(null);
            $table->char('pic14', 1)->nullable()->default(null);
            $table->char('pic15', 1)->nullable()->default(null);
            $table->char('pic16', 1)->nullable()->default(null);
            $table->char('pic17', 1)->nullable()->default(null);
            $table->char('pic18', 1)->nullable()->default(null);
            $table->char('pic19', 1)->nullable()->default(null);
            $table->char('pic20', 1)->nullable()->default(null);

            $table->timestamp('insdatetime')->nullable()->useCurrent();
            $table->integer('insuserno')->nullable();
            $table->timestamp('upddatetime')->nullable()->useCurrentOnUpdate();
            $table->integer('upduserno')->nullable();

            $table->integer('item68');
            $table->integer('item69');
            $table->integer('item70');
            $table->integer('item71');
            $table->integer('item72');

            $table->text('questurl', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facility');
    }
}
