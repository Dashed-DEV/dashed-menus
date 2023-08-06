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
        foreach (\Dashed\DashedMenus\Models\MenuItem::withTrashed()->get() as $menuItem) {
            $menuItem->model = str_replace('Dashed\DashedCore\Models\Page', 'Dashed\DashedPages\Models\Page', $menuItem->model);
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
