<?php

namespace Dashed\DashedMenus\Classes;

use Dashed\DashedMenus\Models\Menu;
use Illuminate\Support\Facades\App;
use Dashed\DashedCore\Classes\Helper;
use Illuminate\Support\Facades\Cache;
use Dashed\DashedCore\Classes\Locales;
use Illuminate\Support\Facades\Artisan;

class Menus
{
    public static function clearCache()
    {
        foreach (Menu::get() as $menu) {
            foreach (Locales::getLocales() as $locale) {
                Cache::forget('menu-' . $menu->name . '-' . $locale['id']);
            }
        }
        Artisan::call('cache:clear');
    }

    public static function getMenuItems($menuName)
    {
        $menuItems = Cache::rememberForever("menu-$menuName-" . App::getLocale(), function () use ($menuName) {
            $menu = Menu::where('name', $menuName)->first();

            if (!$menu) {
                return [];
            }

            $topLevelMenuItems = $menu->parentMenuItems()->with(['childMenuItems'])->thisSite()->get();
            return self::processMenuItems($topLevelMenuItems);
        });

        $menuItems = self::setActiveStatus($menuItems);

        return $menuItems;
    }

    public static function processMenuItems($menuItems)
    {
        $result = [];
        foreach ($menuItems as $menuItem) {
            $childMenuItems = $menuItem->childMenuItems()->with(['childMenuItems'])->get();
            $result[] = [
                'id' => $menuItem->id,
                'name' => $menuItem->name(),
                'url' => $menuItem->getUrl(),
                'type' => $menuItem->type,
                'contentBlocks' => $menuItem->contentBlocks,
                'hasChilds' => $childMenuItems->isNotEmpty(),
                'childs' => $childMenuItems->isNotEmpty() ? self::processMenuItems($childMenuItems) : [],
            ];
        }
        return $result;
    }

    public static function setActiveStatus($menuItems): array
    {
        foreach ($menuItems as &$menuItem) {
            $menuItem['active'] = Helper::urlIsActive($menuItem['url'], true);
            if ($menuItem['hasChilds']) {
                self::setActiveStatus($menuItem['childs']);
            }
        }

        return $menuItems;
    }
}
