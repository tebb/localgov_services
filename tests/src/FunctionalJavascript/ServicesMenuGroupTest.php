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

  /**
   * Check menu structure is as expected.
   */
  public function testServicesLinkGroupItemVisibility() {

    $parameters = new MenuTreeParameters();
    $parameters->onlyEnabledLinks();

    /** @var \Drupal\Core\Menu\MenuLinkTreeElement[] $admin_menu_tree */
    $admin_menu = \Drupal::service('menu.link_tree')->load('admin', $parameters);

    $this->assertCount(1, $admin_menu, 'Admin menu only has 1 top level items.');

    $this->assertArrayHasKey('system.admin', $admin_menu);

    $this->assertTrue($admin_menu['system.admin']->hasChildren, 'System admin has children');

    /** @var \Drupal\Core\Menu\MenuLinkTreeElement[] $system_admin */

    $system_admin = $admin_menu['system.admin']->subtree;

    $this->assertCount(9, $system_admin, 'System admin only has 9 items.');

    $this->assertArrayHasKey('system.admin_content', $system_admin);
    $this->assertArrayHasKey('system.admin_structure', $system_admin);
    $this->assertArrayHasKey('system.themes_page', $system_admin);
    $this->assertArrayHasKey('system.modules_list', $system_admin);
    $this->assertArrayHasKey('system.admin_config', $system_admin);
    $this->assertArrayHasKey('system.admin_reports', $system_admin);
    $this->assertArrayHasKey('entity.user.collection', $system_admin);
    $this->assertArrayHasKey('help.main', $system_admin);
    $this->assertArrayHasKey('admin_toolbar_tools.help', $system_admin);

    $this->assertTrue($system_admin['system.admin_content']->hasChildren, 'System admin content has children');

    /** @var \Drupal\Core\Menu\MenuLinkTreeElement[] $system_admin_content */

    $system_admin_content = $system_admin['system.admin_content']->subtree;

    $this->assertCount(5, $system_admin_content, 'System admin content only has 5 items.');

    // $this->assertArrayHasKey('add_content', $system_admin);
    // $this->assertArrayHasKey('system.admin_structure', $system_admin);
    // $this->assertArrayHasKey('system.themes_page', $system_admin);
    // $this->assertArrayHasKey('system.modules_list', $system_admin);
    // $this->assertArrayHasKey('system.admin_config', $system_admin);


  }

}
