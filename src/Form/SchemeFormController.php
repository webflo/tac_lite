<?php

/**
 * @file
 * Contains \Drupal\tac_lite\Form\SchemeFormController.
 */

namespace Drupal\tac_lite\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;

class SchemeFormController extends EntityForm {

  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#description' => t('A human-readable name for administrators to see. For example, \'read\' or \'read and write\'.'),
      '#default_value' => $this->entity->label,
      '#required' => TRUE,
    );

    $form['name'] = array(
      '#type' => 'machine_name',
      '#default_value' => $this->entity->name,
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => array(
        'exists' => 'Drupal\tac_lite\Entity\Scheme::load',
        'source' => array('label'),
      ),
    );

    // Currently, only view, update and delete are supported by node_access
    $options = array(
      'grant_view' => 'View',
      'grant_update' => 'Update',
      'grant_delete' => 'Delete',
    );

    $form['permissions'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Permissions'),
      '#multiple' => TRUE,
      '#options' => $options,
      '#default_value' => $this->entity->permissions,
      '#description' => t('Select which permissions are granted by this scheme.  <br/>Note when granting update, it is best to enable visibility on all terms.  Otherwise a user may unknowingly remove invisible terms while editing a node.'),
      '#required' => FALSE,
    );

    $form['unpublished_content'] = array(
      '#type' => 'checkbox',
      '#title' => t('Apply to unpublished content'),
      '#description' => t('If checked, permissions in this scheme will apply to unpublished content.  If this scheme includes the view permission, then <strong>unpublished nodes will be visible</strong> to users whose roles would grant them access to the published node.'),
      '#default_value' => $this->entity->unpublished_content,
    );

    $form['term_visibility'] = array(
      '#type' => 'checkbox',
      '#title' => t('Visibility'),
      '#description' => t('If checked, this scheme determines whether a user can view <strong>terms</strong>.  Note the <em>view</em> permission in the select field above refers to <strong>node</strong> visibility.  This checkbox refers to <strong>term</strong> visibility, for example in a content edit form or tag cloud.'),
      '#default_value' => $this->entity->term_visibility,
    );

    return $form;
  }

  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
  }

}
