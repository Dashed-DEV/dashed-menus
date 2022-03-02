<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateMenuItemClassesToNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\Qubiqx\QcommerceMenus\Models\MenuItem::withTrashed()->get() as $menuItem) {
            $menuItem->model = str_replace('Qubiqx\Qcommerce\Models\Page', 'Qubiqx\QcommercePages\Models\Page', $menuItem->model);
            $siteIds = [];
            foreach ($menuItem->site_ids as $siteIdKey => $siteId) {
                $siteIds[$siteIdKey] = $siteIdKey;
            }
            $menuItem->site_ids = $siteIds;
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
        Schema::table('new', function (Blueprint $table) {
            //
        });
    }
}
