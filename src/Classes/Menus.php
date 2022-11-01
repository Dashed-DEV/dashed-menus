<?php

namespace Qubiqx\QcommerceMenus\Classes;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Qubiqx\QcommerceMenus\Models\Menu;
use Qubiqx\QcommerceCore\Classes\Helper;

class Menus
{
    public static function getMenuItems($menuName)
    {
        //Todo: change caching tags to dynamic load models that are used to create routes
        $menuItems = Cache::tags(['menus', 'menu-items', 'products', 'product-categories', 'pages'])->rememberForever("menu-$menuName-" . App::getLocale(), function () use ($menuName) {
            $menu = Menu::where('name', $menuName)->first();

            if ($menu) {
                $menuItems = [];
                foreach ($menu->parentMenuItems()->with(['childMenuItems'])->thisSite()->get() as $menuItem) {
                    $childMenuItems = $menuItem->childMenuItems()->with(['childMenuItems'])->get();
                    $childs = [];
                    foreach ($childMenuItems as $childMenuItem) {
                        $child2MenuItems = $childMenuItem->childMenuItems()->with(['childMenuItems'])->get();
                        $childs2 = [];
                        foreach ($child2MenuItems as $child2MenuItem) {
                            $childs2[] = [
                                'id' => $child2MenuItem->id,
                                'name' => $child2MenuItem->name(),
                                'url' => $child2MenuItem->getUrl(),
                                'type' => $child2MenuItem->type,
                                'contentBlocks' => $child2MenuItem->contentBlocks,
                                'hasChilds' => false,
                                'childs' => [],
                            ];
                        }

                        $childs[] = [
                            'id' => $childMenuItem->id,
                            'name' => $childMenuItem->name(),
                            'url' => $childMenuItem->getUrl(),
                            'type' => $childMenuItem->type,
                            'contentBlocks' => $childMenuItem->contentBlocks,
                            'hasChilds' => count($childs2) ? true : false,
                            'childs' => $childs2,
                        ];
                    }

                    $menuItems[] = [
                        'id' => $menuItem->id,
                        'name' => $menuItem->name(),
                        'url' => $menuItem->getUrl(),
                        'type' => $menuItem->type,
                        'contentBlocks' => $menuItem->contentBlocks,
                        'hasChilds' => count($childs) ? true : false,
                        'childs' => $childs,
                    ];
                }

                return $menuItems;
            }

            return [];
        });

        foreach ($menuItems as &$menuItem) {
            $menuItem['active'] = Helper::urlIsActive($menuItem['url'], true);
            if ($menuItem['hasChilds']) {
                foreach ($menuItem['childs'] as &$childLevel1Item) {
                    $childLevel1Item['active'] = Helper::urlIsActive($childLevel1Item['url'], true);
                    if ($childLevel1Item['hasChilds']) {
                        foreach ($childLevel1Item['childs'] as &$childLevel2Item) {
                            $childLevel2Item['active'] = Helper::urlIsActive($childLevel2Item['url'], true);
                        }
                    }
                }
            }
        }

        return $menuItems;
    }
}
