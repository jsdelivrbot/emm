<?php

namespace Drupal\browscap;

use Drupal\Component\Utility\Html;
use GuzzleHttp\Exception\RequestException;

class BrowscapEndpoint {

  public function getVersion() {
    $config = \Drupal::config('browscap.settings');
    // Retrieve the current browscap data version number using HTTP
    $client = \Drupal::httpClient();
    try {
      $browscapVersionURL = $config->get('version_url');
      $response = $client->get($browscapVersionURL);
      // Expected result.
      $current_version = (string) $response->getBody();
    } catch (RequestException $e) {
      \Drupal::logger('browscap')->error($e->getMessage());
    }

    // Log an error if the browscap version number could not be retrieved
    if (isset($current_version->error)) {
      // Log a message with the watchdog
      \Drupal::logger('browscap')
        ->error("Couldn't check version: %error", ['%error' => $current_version->error]);
      return BrowscapImporter::BROWSCAP_IMPORT_VERSION_ERROR;
    }

    // Sanitize the returned version number
    $current_version = Html::escape(trim($current_version));

    return $current_version;
  }

  public function getBrowscapData($cron = TRUE) {
    $config = \Drupal::config('browscap.settings');
    $client = \Drupal::httpClient();

    // Set options for downloading data with or without compression.
    if (function_exists('gzdecode')) {
      $options = [
        'headers' => ['Accept-Encoding' => 'gzip'],
      ];
    } else {
      // The download takes over ten times longer without gzip, and may exceed
      // the default timeout of 30 seconds, so we increase the timeout.
      $options = ['timeout' => 600];
    }

    // Retrieve the browscap data using HTTP
    try {
      $browscapDataURL = $config->get('data_url');
      $response = $client->get($browscapDataURL, $options);

      // getBody will decompress gzip if need be
      $browscap_data = (string) $response->getBody();
      // Expected result.
    } catch (RequestException $e) {
      watchdog_exception('browscap', $e->getMessage());
    }

    // Log an error if the browscap data could not be retrieved
    if (isset($response->error) || empty($response)) {
      // Log a message with the watchdog
      \Drupal::logger('browscap')
        ->error("Couldn't retrieve updated browscap: %error", ['%error' => $browscap_data->error]);

      // Display a message to the user if the update process was triggered manually
      if ($cron == FALSE) {
        drupal_set_message(t("Couldn't retrieve updated browscap: %error", ['%error' => $response->error]), 'error');
      }

      return BrowscapImporter::BROWSCAP_IMPORT_DATA_ERROR;
    }

    return $browscap_data;
  }
}