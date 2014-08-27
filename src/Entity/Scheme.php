<?php

/**
 * @file
 * Contains \Drupal\tac_lite\Entity\Scheme.
 */

namespace Drupal\tac_lite\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Annotation\ConfigEntityType;

/**
 * @ConfigEntityType(
 *   id = "tac_lite_scheme",
 *   label = @Translation("TAC Lite Scheme"),
 *   admin_permission = "administer tac_lite",
 *   config_prefix = "scheme",
 *   entity_keys = {
 *     "id" = "name",
 *     "label" = "label"
 *   },
 *   controllers = {
 *     "form" = {
 *       "add" = "Drupal\tac_lite\Form\SchemeFormController",
 *       "edit" = "Drupal\tac_lite\Form\SchemeFormController",
 *     }
 *   }
 * )
 */
class Scheme extends ConfigEntityBase {

  public $name;

  public $label;

  public $unpublished_content;

  public $term_visibility;

  public $form_visibility;

  public $permissions;

  public $weight;

  public function id() {
    return $this->name;
  }

  public function realm() {
    return 'tac_lite_scheme_' . $this->id();
  }

}
