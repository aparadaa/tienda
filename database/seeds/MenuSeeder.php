<?php
use App\Models\Menu\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $sections = new Sections;
        $sections = $sections->get();

        Menu::truncate();

        foreach ($sections as $section) {
            if ($section->menuPermission === 0) {
                continue;
            }
            $menuItem = new Menu;

            $menuItem->icon        = $section->icon;
            $menuItem->name        = $section->name;
            $menuItem->route       = $section->module . (!empty($section->permissions) ? '.' . $section->menuPermission : '');
            $menuItem->order       = $section->menuOrder;
            $menuItem->external_id = $section->externalId;

            $menuItem->has_children = empty($section->permissions);
            if ($section->parentModule) {
                $menuItem->parent_route = $section->parentModule;
            }
            $menuItem->save();
        }
        Cache::flush();
    }
}
