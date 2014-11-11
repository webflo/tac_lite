<?php

/**
 * @file
 * Contains \Drupal\tac_lite\Form\SchemeDeleteForm.
 */

namespace Drupal\tac_lite\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

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
  public function getCancelUrl() {
    return Url::fromRoute('tac_lite.scheme_list');
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();
    drupal_set_message($this->t('Deleted scheme %name.', array('%name' => $this->entity->label())));
    $this->logger('tac_lite')->notice('Deleted scheme %name.', array('%name' => $this->entity->label()));
    $form_state->setRedirect('tac_lite.scheme_list');
  }

}
