<?
class MenuLocation
{
    static $options;
    static $themeLocation;

    public static function start($options)
    {
        $defaultOptions = array(
            'theme_location'         => null,
            'order'                  => 'ASC',
            'orderby'                => 'menu_order',
            'output_key'             => 'menu_order',
            'update_post_term_cache' => false
        );
        self::$options = array_merge($defaultOptions, $options);


        $menu_id = null;

        if(self::$options['theme_location'] == null) return false;

        $locs = get_nav_menu_locations();

        if(!isset($locs[self::$options['theme_location']])) return false;
        $menu_id = $locs[self::$options['theme_location']];
        
        self::$themeLocation = self::$options['theme_location'];

        $argsSettings = self::$options;
        unset($argsSettings['theme_location']);
        
        $argsDefault = array( 'order' => 'ASC', 'orderby' => 'menu_order', 'output' => ARRAY_A, 'output_key' => 'menu_order', 'update_post_term_cache' => false);
        $args = array_merge($argsDefault, $argsSettings);

        $wrapper = "";

        $items = wp_get_nav_menu_items( $menu_id, $args );
        $arMenu = array();
        foreach($items as $item)
        {
            if($item->menu_item_parent == 0)
            {
                $settings_menu = array();

                $child = self::gen_item_catalog($item, $items, $item->ID);

                $itemHtml = apply_filters( 'menu_location_' . self::$themeLocation . '_item', $item, $child);

                $wrapper .= $itemHtml;
            }
        }

        $output = apply_filters( 'menu_location_' . self::$themeLocation . '_wrapper', $wrapper);

        return $output;
    }

    private static function gen_item_catalog($parentItem, $items, $id_parent, $level = 2)
    {
        $wrapper = "";
        $itemHtml = "";
        $count = 0;
        foreach($items as $item)
        {
            if($parentItem->ID == $item->menu_item_parent)
            {
                $child = self::gen_item_catalog($item, $items, $id_parent, $level + 1);
                $itemHtml .= apply_filters( 'submenu_location_' . self::$themeLocation . '_item', $item, $child, $level);

                $count++;
            }
        }
        $wrapper = apply_filters( 'submenu_location_' . self::$themeLocation . '_wrapper', $itemHtml, $level, $parentItem);
        return array('html' => $wrapper, 'count' => $count);
    }
}
//Call echo MenuLocation::start(array('theme_location' => 'pages'));
//Example add_filter
add_filter( 'menu_location_pages_wrapper', 'menu_location_pages_wrapper', 10, 1 );
function menu_location_pages_wrapper($wrapper) {
    return '<div class="links">'.$wrapper.'</div>';
}


add_filter( 'submenu_location_pages_wrapper', 'submenu_location_pages_wrapper', 10, 3 );
function submenu_location_pages_wrapper($wrapper, $level, $parent) {
    return '<ul class="dropdown-menu submenu-'.$level.'" id="parent-'.$parent->object_id.'">' . $wrapper . '</ul>';
}


add_filter( 'menu_location_pages_item', 'menu_location_pages_item', 10, 2 );
function menu_location_pages_item($data, $child) {
    if($child['count'] > 0) {
        return '<div class="item '.implode(' ', $data->classes).' dropdown" id="item-'.$data->object_id.'"><a role="button" data-toggle="dropdown" data-target="parent-'.$data->ID.'" href="'.$data->url.'" class="dropdown-toggle">'.$data->title.' <span class="caret"></span></a>'.$child['html'].'</div>';
    }

    return '<div class="item '.implode(' ', $data->classes).'" id="item-'.$data->object_id.'"><a href="'.$data->url.'"><span class="hidden-xs">'.$data->title.'</span></a></div>';
}


add_filter( 'submenu_location_pages_item', 'submenu_location_pages_item', 10, 3 );
function submenu_location_pages_item($data, $child, $level) {
    if($child['count'] > 0) {
        return '<li role="presentation" class="'.implode(' ', $data->classes).'" id="item-'.$data->object_id.'"><a href="'.$data->url.'">'.$data->title.'</a> </li>';
    }

    return '<li role="presentation" class="'.implode(' ', $data->classes).'" id="item-'.$data->object_id.'"><a href="'.$data->url.'">'.$data->title.'</a> </li>';
}