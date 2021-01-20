<?php

namespace Drupal\Tests\localgov_services\FunctionalJavascript;

use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;


/**
 * Javascript tests for LocalGovDrupal services menu link group.
 */
class ServicesMenuGroupTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'localgov_theme';

  /**
   * {@inheritdoc}
   */
  protected $profile = 'localgov';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'localgov_services',
    'localgov_services_landing',
    'localgov_services_navigation',
    'localgov_services_page',
    'localgov_services_status',
    'localgov_services_sublanding',
    'localgov_menu_link_group',
  ];

  public function testServicesLinkGroupItemVisibility() {

    $parameters = new MenuTreeParameters();
    $parameters->onlyEnabledLinks();

    /** @var \Drupal\Core\Menu\MenuLinkTreeElement[] $admin_menu_tree */
    $admin_menu = \Drupal::service('menu.link_tree')->load('admin', $parameters);

    $this->assertCount(9, $admin_menu, 'Admin menu only has 9 top level items.');
    # TODO: Determine correct route names etc
    $this->assertArrayHasKey('admin.tools', $admin_menu);
    $this->assertArrayHasKey('admin.tools', $admin_menu);
    $this->assertArrayHasKey('admin.tools', $admin_menu);

    // Check the Route2 menu subtree.
    $this->assertTrue($admin_menu['my.route2']->hasChildren, 'Route2 menu has children');
    /** @var \Drupal\Core\Menu\[] $route2_menu */
    $route2_menu = $admin_menu['my.route2']->subtree;

    $this->assertCount(2, $route1_menu, 'Route1 menu has exactly 2 children');
    $this->assertArrayHasKey('my.submroute1', $route1_menu);
    $this->assertArrayHasKey('my.submroute2', $route1_menu);

    // Check an item without children.
    this->assertFalse($main_menu_tree['my.route2']->hasChildren, 'Route2 menu has no children');

  }

}
