<?php

/**
 * @file
 * Contains \Drupal\tac_lite\Form\SchemeDeleteForm.
 */

namespace Drupal\tac_lite\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;

class SchemeDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tac_lite_scheme_confirm_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the scheme %title?', array('%title' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'tac_lite.scheme_list',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    drupal_set_message($this->t('Deleted scheme %name.', array('%name' => $this->entity->label())));
    watchdog('tac_lite', 'Deleted scheme %name.', array('%name' => $this->entity->label()), WATCHDOG_NOTICE);
    $form_state['redirect_route']['route_name'] = 'tac_lite.scheme_list';
  }

}
