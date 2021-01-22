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



$system_admin_content =  $admin_menu_tree["system.admin"]->subtree["system.admin_content"]->subtree;
