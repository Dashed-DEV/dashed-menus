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
            $customBlock = new \Dashed\DashedCore\Models\CustomBlock();
            $customBlock->blocks = $menuItem->blocks;
            $customBlock->blockable_type = \Dashed\DashedMenus\Models\MenuItem::class;
            $customBlock->blockable_id = $menuItem->id;
            $customBlock->save();
        }

        Schema::dropColumns('dashed__menu_items', ['blocks']);
    }
};
