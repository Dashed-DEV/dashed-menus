<?php

namespace Dashed\DashedMenus\Models;

use Dashed\DashedMenus\Classes\Menus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Menu extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected static $logFillable = true;

    protected $table = 'dashed__menus';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function booted()
    {
        static::created(function ($menu) {
            Menus::clearCache();
        });

        static::updated(function ($menu) {
            Menus::clearCache();
        });

        static::deleting(function ($menu) {
            $menu->menuItems()->delete();
        });
    }

    protected $casts = [
        'items' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class)->with(['parentMenuItem']);
    }

    public function parentMenuItems()
    {
        return $this->hasMany(MenuItem::class)->where('parent_menu_item_id', null)->orderBy('order', 'ASC');
    }
}
