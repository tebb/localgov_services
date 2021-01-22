<?php

// >>> gettype(((array)$admin_menu_tree["system.admin"])["subtree"]);

// $SASubtree=((array)$admin_menu_tree["system.admin"])["subtree"];
// array_keys($SASubtree)
// $SASubtree["system.admin_content"];
// gettype($SASubtree["system.admin_content"]);
// ((array)$SASubtree["system.admin_content"]);
// array_keys((array)$SASubtree["system.admin_content"]);
// ((array)$SASubtree["system.admin_content"])["depth"];


use Drupal\Core\Menu\MenuTreeParameters;
$parameters = new MenuTreeParameters();
$parameters->onlyEnabledLinks();
$admin_menu_tree = \Drupal::service('menu.link_tree')->load('admin', $parameters);


// array_keys(((array)$admin_menu_tree["system.admin"])["subtree"]);


system_admin_content =  $admin_menu_tree["system.admin"]->subtree["system.admin_content"]->subtree;

>>> $system_admin_content=((array)$admin_menu_tree["system.admin"])["subtree"]["system.admin_content"]->subtree



>>> gettype($system_admin_content=((array)$admin_menu_tree["system.admin"])["subtree"]["system.admin_content"]->subtree)
=> "array"
>>> array_keys($system_admin_content=((array)$admin_menu_tree["system.admin"])["subtree"]["system.admin_content"]->subtree)
=> [
     "admin_toolbar_tools.extra_links:node.add",
     "admin_toolbar_tools.extra_links:media_page",
     "paragraphs_library.paragraphs_library_item.collection",
     "admin_toolbar_tools.extra_links:view.files",
     "entity.localgov_directories_facets.collection",
   ]
>>>

// Menu item details Level 3
$system_admin_content_add_node=$admin_menu_tree["system.admin"]->subtree["system.admin_content"]->subtree["admin_toolbar_tools.extra_links:node.add"
]->hasChildren


>>> array_keys($system_admin_content_add_node=$admin_menu_tree["system.admin"]->subtree["system.admin_content"]->subtree["admin_toolbar_tools.extra_links:node.add"
]->subtree)
=> [
     "admin_toolbar_tools.extra_links:node.add.localgov_services_status",
     "localgov_menu_link_group:admin_toolbar_tools.extra_links:node.add:services",
     "localgov_menu_link_group:admin_toolbar_tools.extra_links:node.add:step_by_step",
     "localgov_menu_link_group:admin_toolbar_tools.extra_links:node.add:subsite",
     "localgov_menu_link_group:admin_toolbar_tools.extra_links:node.add:guide",
     "localgov_menu_link_group:admin_toolbar_tools.extra_links:node.add:directory",
   ]
>>>