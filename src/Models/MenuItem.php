<?php

namespace Qubiqx\QcommerceMenus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Qubiqx\QcommerceCore\Classes\Sites;
use Qubiqx\QcommerceCore\Models\Concerns\HasCustomBlocks;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    use SoftDeletes;
    use HasTranslations;
    use LogsActivity;
    use HasCustomBlocks;

    protected static $logFillable = true;

    protected $table = 'qcommerce__menu_items';

    public $translatable = [
        'name',
        'url',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'contentBlocks',
    ];

    protected $casts = [
        'site_ids' => 'array',
        'blocks' => 'array',
    ];

    protected static function booted()
    {
        static::created(function ($menuItem) {
            Cache::tags(['menu-items'])->flush();
        });

        static::updated(function ($menuItem) {
            Cache::tags(['menu-items'])->flush();
        });

        static::deleting(function ($menuItem) {
            foreach ($menuItem->getChilds() as $child) {
                $child->delete();
            }
        });

        static::deleted(function ($menuItem) {
            Cache::tags(['menu-items'])->flush();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function scopeThisSite($query)
    {
        $query->whereJsonContains('site_ids', Sites::getActive());
    }

    public function scopeSearch($query, ?string $search = null)
    {
        if (request()->get('search') ?: $search) {
            $search = strtolower(request()->get('search') ?: $search);
            $query->where('site_ids', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%")
                ->orWhere('url', 'LIKE', "%$search%")
                ->orWhere('type', 'LIKE', "%$search%")
                ->orWhere('model', 'LIKE', "%$search%");
        }
    }

    public function site()
    {
        foreach (Sites::getSites() as $site) {
            if ($site['id'] == $this->site_id) {
                return $site;
            }
        }
    }

    public function activeSiteIds()
    {
        $menuItem = $this;
        while ($menuItem->parent_menu_item_id) {
            $menuItem = self::find($menuItem->parent_menu_item_id);
            if (!$menuItem) {
                return;
            }
        }

        $sites = [];
        foreach (Sites::getSites() as $site) {
            if (self::where('id', $menuItem->id)->where('site_ids->' . $site['id'], 'active')->count()) {
                array_push($sites, $site['id']);
            }
        }

        return $sites;
    }

    public function siteNames()
    {
        $menuItem = $this;
        while ($menuItem->parent_menu_item_id) {
            $menuItem = self::find($menuItem->parent_menu_item_id);
            if (!$menuItem) {
                return;
            }
        }

        $sites = [];
        foreach (Sites::getSites() as $site) {
            if (self::where('id', $menuItem->id)->where('site_ids->' . $site['id'], 'active')->count()) {
                $sites[$site['name']] = 'active';
            } else {
                $sites[$site['name']] = 'inactive';
            }
        }

        return $sites;
    }

    public function getChilds()
    {
        $childs = [];
        $childMenuItems = self::where('parent_menu_item_id', $this->id)->orderBy('order', 'DESC')->get();
        while ($childMenuItems->count()) {
            $childMenuItemIds = [];
            foreach ($childMenuItems as $childMenuItem) {
                $childMenuItemIds[] = $childMenuItem->id;
                $childs[] = $childMenuItem;
            }
            $childMenuItems = self::whereIn('parent_menu_item_id', $childMenuItemIds)->get();
        }

        return $childs;
    }

    public function getUrl()
    {
        return Cache::tags(['menus', 'menu-items', 'products', 'product-categories', 'pages', 'articles', "menuitem-$this->id"])->remember("menuitem-url-$this->id-" . App::getLocale(), 60 * 60 * 24, function () {
            if (!$this->type || $this->type == 'normal' || $this->type == 'externalUrl') {
                if ($this->url && (parse_url($this->url)['host'] ?? request()->getHttpHost()) != request()->getHttpHost()) {
                    return $this->url;
                } else {
                    return LaravelLocalization::localizeUrl($this->url ?: '/');
                }
            } else {
                $modelResult = $this->model::find($this->model_id);
                if ($modelResult) {
                    $url = $modelResult->getUrl();

                    return $url ?: '/';
                } else {
                    return '/';
                }
            }
        });
    }

    public function name(): string
    {
        return Cache::tags(['menus', 'menu-items', "menuitem-$this->id"])->remember("menuitem-name-$this->id-" . App::getLocale(), 60 * 60 * 24, function () {
            if (!$this->type || $this->type == 'normal' || $this->type == 'externalUrl') {
                return $this->name;
            } else {
                $modelResult = $this->model::find($this->model_id);
                $replacementName = '';
                if ($modelResult) {
                    $replacementName = $modelResult->name;
                    if (!$replacementName) {
                        $replacementName = $modelResult->title;
                    }
                }

                return str_replace(':name:', $replacementName, $this->name);
            }
        });
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function childMenuItems(): HasMany
    {
        return $this->hasMany(self::class, 'parent_menu_item_id')->orderBy('order', 'ASC');
    }

    public function parentMenuItem(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_menu_item_id');
    }
}
