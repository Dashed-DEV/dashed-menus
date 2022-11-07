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
        foreach (\Qubiqx\QcommerceMenus\Models\MenuItem::withTrashed()->get() as $menuItem) {
            $customBlock = new \Qubiqx\QcommerceCore\Models\CustomBlock();
            $customBlock->blocks = $menuItem->blocks;
            $customBlock->blockable_type = \Qubiqx\QcommerceMenus\Models\MenuItem::class;
            $customBlock->blockable_id = $menuItem->id;
            $customBlock->save();
        }

        Schema::dropColumns('qcommerce__menu_items', ['blocks']);
    }
};
