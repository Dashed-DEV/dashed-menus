<?php

use Illuminate\Database\Migrations\Migration;

class TransformPageClassesInMenuToNewClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\Qubiqx\QcommerceMenus\Models\MenuItem::withTrashed()->get() as $menuItem) {
            $menuItem->model = str_replace('Qubiqx\QcommerceCore\Models\Page', 'Qubiqx\QcommercePages\Models\Page', $menuItem->model);
            $menuItem->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
