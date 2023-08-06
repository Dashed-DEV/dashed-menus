<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\Dashed\DashedMenus\Models\MenuItem::withTrashed()->get() as $menuItem) {
            $menuItem->type = str($menuItem->type)->camel();
            $menuItem->save();
        }
    }
};
