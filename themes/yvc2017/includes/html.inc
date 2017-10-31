<?php
/**
 * @file
 * HTML template functions.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Render\Markup;

/**
 * Implements hook_preprocess_html().
 */
function yvc2017_preprocess_html(array &$vars, $hook) {
  // Add x-ua-compatible meta tag.
  $vars['page']['#attached']['html_head'][] = [
    array(
      '#tag' => 'meta',
      '#attributes' => array(
        'http-equiv' => 'x-ua-compatible',
        'content' => 'ie=edge',
      ),
    ),
    'x_ua_compatible'
  ];

  // DevCollab Customization - Add body class for content id
  $vars['path_info']['args'] = FALSE;
  $path = \Drupal::service('path.current')->getPath();
  $path_args = explode('/', $path);
  if (count($path_args) >= 3) {
    $vars['path_info']['args'] = $path_args[2];
  }
}

/**
 * Implements hook_preprocess_maintenance_page().
 */
function yvc2017_preprocess_maintenance_page(array &$vars, $hook) {
  // While markup for normal pages is split into html.html.twig and
  // page.html.twig, the markup for the maintenance page is all in the single
  // maintenance-page.html.twig template. So, to have what’s done in
  // yvc2017_preprocess_html() and yvc2017_preprocess_page() also happen
  // on the maintenance page, it has to be called here.
  yvc2017_preprocess_html($vars, $hook);
  yvc2017_preprocess_page($vars, $hook);
}

/**
 * Implements hook_preprocess_page().
 */
function yvc2017_preprocess_page(array &$vars) {
  // Only check certain regions for emptiness, so that we don't bog the entire
  // page down with pre-rendering logic.
  $check_regions = array_intersect(Element::children($vars['page']), ['sidebar_second']);

  // Get rid of this 'workaround' as soon as humanly possible (i.e., when Drupal core
  // has a consistent definition of what "empty" is in a rendering context).
  foreach ($check_regions as $region) {
    if (_yvc2017_region_is_empty($vars['page'][$region])) {
      $vars['page'][$region] = [];
    }
  }
}

/**
 * Checks if a region is empty by pre-rendering junk.
 *
 * This is a terrible hack around the fact that Drupal's rendering system really
 * can't reliably tell if things are "empty". This is a terrible long-standing
 * issue (8 years and counting!) that deserves a terrible fix.
 *
 * @param array $region
 *   The renderable region.
 *
 * @return bool
 *   TRUE if everything in the region will produce empty output; FALSE
 *   otherwise.
 *
 * @see https://www.drupal.org/node/2443457
 * @see https://www.drupal.org/node/953034
 */
function _yvc2017_region_is_empty(array $region) {
  foreach (Element::children($region) as $element) {
    $rendered_element = trim(render($region[$element]));
    if ($rendered_element) {
      return FALSE;
    }
  }
  return TRUE;
}
