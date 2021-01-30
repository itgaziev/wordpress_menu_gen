# wordpress_menu_gen
Wordpress Генерация меню с помощью фильтров

# Call echo MenuLocation::start(array('theme_location' => 'pages'));
# Example add_filter
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
