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
 *     "label" = "label",
 *     "weight" = "weight",
 *   },
 *   handlers = {
 *     "list_builder" = "Drupal\tac_lite\SchemeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\tac_lite\Form\SchemeFormController",
 *       "edit" = "Drupal\tac_lite\Form\SchemeFormController",
 *       "delete" = "Drupal\tac_lite\Form\SchemeDeleteForm"
 *     }
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/people/tac_lite/manage/{tac_lite_scheme}",
 *     "delete-form" = "/admin/config/people/tac_lite/manage/{tac_lite_scheme}/delete"
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

  public function hasPermission($permission) {
    return !empty($this->permissions[$permission]);
  }

}
