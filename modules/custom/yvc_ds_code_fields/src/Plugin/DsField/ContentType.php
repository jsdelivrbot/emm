<?php

namespace Drupal\yvc_ds_code_fields\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Plugin that renders the label of the Content Type.
 *
 * @DsField(
 *   id = "content_type",
 *   title = @Translation("DS: Content Type"),
 *   entity_type = "node",
 *   provider = "yvc_ds_code_fields",
 *   ui_limit = {"collection|full","resource|full","initiative|full",}
 * )
 */
class ContentType extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Custom Display Suite code field for showing Content Type label
    // on Collections, Initiatives and Resources
    // See:
    // https://www.sitepoint.com/custom-display-suite-fields-in-drupal-8/

    // Get current node
    $node = \Drupal::routeMatch()->getParameter('node');
    $tid = '0';

    // If node has a Resource Type, then it is a Resource
    // Get its TID so we can choose its output (Resource or Toolkit)
    if ($node->hasField('field_resource_type')) {
      $tid = $node->get('field_resource_type')->target_id;
    }

    // If the Content Type is Resource and the
    // Resource type TID is 10 (Toolkit) change output to Toolkit
    if ($node->type->entity->label() == "Resource" && $tid == '10') {
      $type = $this->t("Toolkit");
    }
    else {
      // Otherwise just output label of the Content Type
      $type = $node->type->entity->label();
    }

    return [
      '#markup' => $type,
    ];
  }
}