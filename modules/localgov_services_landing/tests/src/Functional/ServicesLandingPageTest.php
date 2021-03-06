<?php

namespace Drupal\Tests\localgov_services_landing\Functional;

use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\NodeCreationTrait;

/**
 * Tests localgov service landing pages.
 *
 * @group localgov_services
 */
class ServicesLandingPageTest extends BrowserTestBase {

  use NodeCreationTrait;


  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'field_ui',
    'localgov_services_landing',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
      'administer node fields',
    ]);
  }

  /**
   * Test necessary fields exist and display correctly.
   */
  public function testServiceLandingPage() {
    $this->drupalLogin($this->adminUser);

    // Check all fields exist.
    $this->drupalGet('/admin/structure/types/manage/localgov_services_landing/fields');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('body');
    $this->assertSession()->pageTextContains('field_address');
    $this->assertSession()->pageTextContains('field_address_first_line');
    $this->assertSession()->pageTextContains('field_common_tasks');
    $this->assertSession()->pageTextContains('field_contact_us_online');
    $this->assertSession()->pageTextContains('field_destinations');
    $this->assertSession()->pageTextContains('field_email_address');
    $this->assertSession()->pageTextContains('field_facebook');
    $this->assertSession()->pageTextContains('field_hearing_difficulties_phone');
    $this->assertSession()->pageTextContains('field_link_to_map');
    $this->assertSession()->pageTextContains('field_opening_hours');
    $this->assertSession()->pageTextContains('field_other_team_contacts');
    $this->assertSession()->pageTextContains('field_phone');
    $this->assertSession()->pageTextContains('field_popular_topics');
    $this->assertSession()->pageTextContains('field_twitter');

    // Check basic landing page.
    $title = $this->randomMachineName(8);
    $summary = $this->randomMachineName(16);
    $page = $this->createNode([
      'type' => 'localgov_services_landing',
      'title' => $title,
      'body' => [
        'summary' => $summary,
        'value' => '',
      ],
      'status' => NodeInterface::PUBLISHED,
    ]);
    $this->drupalGet('/node/' . $page->id());
    $this->assertSession()->pageTextContains($title);
    $this->assertSession()->pageTextNotContains('Contact this service');
    $this->assertSession()->pageTextNotContains('Popular topics');

    // Check child pages.
    $child = [];
    for ($i = 1; $i <= 2; $i++) {
      $child[] = $this->createNode([
        'type' => 'localgov_services_page',
        'title' => 'Test service page ' . $i,
        'body' => [
          'summary' => 'Test services page summary ' . $i,
          'value' => '',
        ],
        'status' => NodeInterface::PUBLISHED,
        'path' => ['alias' => '/test-' . $i],
      ]);
    }
    $page->field_destinations->appendItem($child[0]);
    $page->field_destinations->appendItem($child[1]);
    $page->save();
    $this->drupalGet('/node/' . $page->id());
    for ($i = 1; $i <= 2; $i++) {
      $this->assertSession()->pageTextContains('Test service page ' . $i);
      $this->assertSession()->pageTextContains('Test services page summary ' . $i);
    }

    // Check contact area.
    $page->set('field_phone', ['value' => '1234567890']);
    $page->save();
    $this->drupalGet('/node/' . $page->id());
    $this->assertSession()->pageTextContains('Contact this service');
    $this->assertSession()->pageTextContains('1234567890');

    // Check popular topics.
    $topic_name = $this->randomMachineName(8);
    $topic = Term::create([
      'name' => $topic_name,
      'vid' => 'topic',
    ]);
    $topic->save();
    $page->set('field_popular_topics', ['target_id' => $topic->id()]);
    $page->save();
    $this->drupalGet('/node/' . $page->id());
    $this->assertSession()->pageTextContains('Popular topics');
    $this->assertSession()->pageTextContains($topic_name);
  }

}
