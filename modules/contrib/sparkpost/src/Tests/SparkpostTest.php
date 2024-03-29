<?php

namespace Drupal\sparkpost\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Test the functionality of the sparkpost module.
 *
 * @group sparkpost
 */
class SparkpostTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['sparkpost'];

  /**
   * Regular user.
   *
   * @var false|object
   */
  protected $user;

  /**
   * Admin user.
   *
   * @var false|object
   */
  protected $admin;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser([]);
    $this->admin = $this->drupalCreateUser([
      'administer sparkpost',
    ]);
  }

  /**
   * Access admin pages.
   */
  public function testAdminAccess() {
    $this->drupalLogin($this->user);

    // Try access sparkpost admin form.
    $this->drupalGet('admin/config/system/sparkpost');
    $this->assertResponse(403);

    // Try access test form.
    $this->drupalGet('admin/config/system/sparkpost/test');
    $this->assertResponse(403);

    // Login as admin.
    $this->drupalLogout();
    $this->drupalLogin($this->admin);

    // Try access sparkpost admin form.
    $this->drupalGet('admin/config/system/sparkpost');
    $this->assertResponse(200);

    // Try access test form.
    $this->drupalGet('admin/config/system/sparkpost/test');
    $this->assertResponse(403);

    // Set dummy access key.
    \Drupal::configFactory()
      ->getEditable('sparkpost.settings')
      ->set('api_key', 'API_KEY')
      ->save();

    // Try access test form.
    $this->drupalGet('admin/config/system/sparkpost/test');
    $this->assertResponse(200);
  }

  /**
   * Test that queue is not processed on cron, if indicated by settings.
   */
  public function testSkipQueueOnCron() {
    \Drupal::configFactory()
      ->getEditable('sparkpost.settings')
      ->set('skip_cron', FALSE)
      ->set('async', TRUE)
      ->save();
    /** @var \Drupal\Core\Queue\QueueInterface $queue */
    $queue = \Drupal::queue('sparkpost_send');
    // Create a dummy item.
    $queue->createItem($this->createDummyMessage());
    $this->assertEqual($queue->numberOfItems(), 1);
    // Run cron.
    $this->cronRun();
    // Should have processed the item.
    $this->assertEqual($queue->numberOfItems(), 0);
    \Drupal::configFactory()
      ->getEditable('sparkpost.settings')
      ->set('skip_cron', TRUE)
      ->save();
    // We also need to clear the cache.
    \Drupal::service('plugin.cache_clearer')->clearCachedDefinitions();
    $queue->createItem($this->createDummyMessage());
    $this->assertEqual($queue->numberOfItems(), 1);
    // Run cron.
    $this->cronRun();
    // Should not have processed the item.
    $this->assertEqual($queue->numberOfItems(), 1);
  }

  /**
   * Helper to create a dummy message.
   *
   * @return \Drupal\sparkpost\MessageWrapperInterface
   *   The message wrapper
   */
  private function createDummyMessage() {
    /** @var \Drupal\sparkpost\MessageWrapperInterface $message */
    $message = \Drupal::service('sparkpost.message_wrapper');
    $message->setDrupalMessage([]);
    $message->setSparkpostMessage([]);
    return $message;
  }

}
