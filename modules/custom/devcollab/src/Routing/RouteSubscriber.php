<?php
/**
 * @file
 * Contains \Drupal\devcollab\Routing\RouteSubscriber.
 */

namespace Drupal\devcollab\Routing;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {
  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Replace "some.route.name" below with the actual route you want to override.
    if ($route = $collection->get('download_count.reports')) {
      $route->setDefaults(array(
        '_controller' => '\Drupal\devcollab\Controller\DevcollabDownloadCountController::downloadCountReport',
      ));
    }
    if ($route = $collection->get('download_count.details')) {
      $route->setDefaults(array(
        '_controller' => '\Drupal\devcollab\Controller\DevcollabDownloadCountController::downloadCountDetails',
      ));
    }
  }
}