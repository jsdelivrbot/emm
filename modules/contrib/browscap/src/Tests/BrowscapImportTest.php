<?php

namespace Drupal\browscap\Tests;

use Drupal\browscap\BrowscapImporter;
use Drupal\simpletest\WebTestBase;

/**
 * Tests import of browscap data
 *
 * @group browscap
 */
class BrowscapImportTest extends WebTestBase {

  public static $modules = ['browscap'];

  /**
   * Tests importing then querying Browscap data.
   */
  public function testImport() {
    $config = \Drupal::config('browscap.settings');
    $versionBefore = $config->get('version');

    $result = BrowscapImporter::import(new MockBrowscapEndpoint());
    $this->assertTrue($result, 'Import completed successfully.');

    $versionAfter = $config->get('version');
    $this->assertNotEqual($versionBefore, $versionAfter, 'Data version number has changed');

    // Make browscap service call to check that data was loaded.
    $user_agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)';
    $properties = \Drupal::service('browscap')->getBrowser($user_agent);
    $this->assertEqual($properties['browser'], "IE", "Uncached browser is correct.");
    $this->assertEqual($properties['version'], "9.0", "Uncached browser version is correct.");
    $this->assertEqual($properties['platform'], "Win7", "Uncached platform is correct.");

    // Now that the data is cached, try again
    $properties = \Drupal::service('browscap')->getBrowser($user_agent);
    $this->assertEqual($properties['browser'], "IE", "Cached browser is correct.");
    $this->assertEqual($properties['version'], "9.0", "Cached browser is correct.");
    $this->assertEqual($properties['platform'], "Win7", "Cached browser is correct.");
  }
}
