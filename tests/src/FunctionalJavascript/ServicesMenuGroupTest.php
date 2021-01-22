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

    $this->assertCount(1, $admin_menu, 'Admin menu only has 1 top level item.');
    // TODO: Determine correct route names etc.
    $this->assertArrayHasKey('system.admin', $admin_menu);
    // $this->assertArrayHasKey('system.admin_content', $admin_menu);
    // $this->assertArrayHasKey('admin.tools', $admin_menu);
  }

}
