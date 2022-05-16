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
    public function up()
    {
        Schema::table('qcommerce__menus', function (Blueprint $table) {
            $table->dropUnique('qcommerce__menus_name_unique');
            $table->string('handle')->unique();
            $table->json('items')->nullable();
        });

        foreach (\Qubiqx\QcommerceMenus\Models\Menu::get() as $menu) {
            $menu->handle = str($menu->name)->slug();
            $menu->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu', function (Blueprint $table) {
            //
        });
    }
};
