<?php

/**
 * @file
 * Contains \Drupal\tac_lite\SettingsForm.
 */

namespace Drupal\tac_lite;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'tact_lite_settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('tac_lite.settings');

    $vocabularies = array();
    foreach (\Drupal::entityManager()->getBundleInfo('taxonomy_term') as $id => $bundle) {
      $vocabularies[$id] = $bundle['label'];
    }

    $form['vocabularies'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Vocabularies'),
      '#description' => t('Select one or more vocabularies to control privacy.  <br/>Use caution with hierarchical (nested) taxonomies as <em>visibility</em> settings may cause problems on node edit forms.<br/>Do not select free tagging vocabularies, they are not supported.'),
      '#options' => $vocabularies,
      '#default_value' => $config->get('vocabularies')
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->get('tac_lite.settings')
      ->set('vocabularies', array_filter($form_state->getValue('vocabularies')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
