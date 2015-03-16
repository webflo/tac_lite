<?php

/**
 * @file
 * Contains \Drupal\tac_lite_ui\Form\TermAccessForm.
 */

namespace Drupal\tac_lite_ui\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\tac_lite\Utility;
use Drupal\user\Entity\User;

class TermAccessForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $permissions = $this->loadPermissions();
    $schemes = $this->loadSchemes();

    $form['list'] = array(
      '#type' => 'table',
      '#header' => array(
        'username' => $this->t('Username'),
      ),
    );

    foreach ($schemes as $scheme) {
      $form['list']['#header'][$scheme->id()] = $scheme->label();
    }

    foreach ($permissions as $uid => $permission) {
      $account = User::load($uid);

      $row = array(
        '#account' => $uid,
      );

      $row['uid'] = array(
        '#markup' => $account->getUsername()
      );

      foreach ($schemes as $scheme) {
        $row[$scheme->id()] = array(
          '#type' => 'checkbox',
          '#title_display' => 'invisible',
          '#parents' => array('list', $account->id(), $scheme->id()),
          '#default_value' => isset($permissions[$account->id()][$scheme->id()]) ? TRUE : FALSE,
        );
      }

      $form['list'][] = $row;
    }

    $form['list']['new_user'] = array(
      '#account' => 0,
    );

    $form['list']['new_user']['username'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#maxlength' => 60,
      '#autocomplete_route_name' => 'user.autocomplete',
      '#default_value' => '',
    );

    foreach ($schemes as $scheme) {
      $form['list']['new_user'][] = array(
        '#type' => 'checkbox',
        '#title_display' => 'invisible',
        '#parents' => array('list', 'new_user', $scheme->id()),
        '#default_value' => isset($permissions['new_user'][$scheme->id()]) ? TRUE : FALSE,
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actionsElement(array $form, FormStateInterface $form_state) {
    $element = parent::actionsElement($form, $form_state);
    unset($element['delete']);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $value = $form_state->getValue(['list', 'new_user']);

    if (!empty($value['username'])) {
      $account = user_load_by_name($value['username']);
      if ($account) {
        unset($value['username']);
        $this->savePermissions($account->id(), $value);
      }
    }

    $list = $form_state->getValue(['list']);
    unset($list['new_user']);
    foreach ($list as $account => $schemes) {
      $this->savePermissions($account, $schemes);
    }
  }

  protected function savePermissions($uid, $schemes) {
    foreach ($schemes as $scheme => $value) {
      if (empty($value)) {
        $this->revokePermission($scheme, $uid, $this->entity->id());
      }
      else {
        $this->grantPermission($scheme, $uid, $this->entity->id());
      }
    }
  }

  protected function grantPermission($scheme, $uid, $tid) {
    return Utility::grantPermission($scheme, $uid, $tid);
  }

  protected function revokePermission($scheme, $uid, $tid) {
    return Utility::revokePermission($scheme, $uid, $tid);
  }


  protected function loadPermissions() {
    $rows = db_select('tac_lite_user', 't')
      ->condition('tid', $this->entity->id())
      ->fields('t', array('uid', 'scheme'))
      ->execute()
      ->fetchAll();
    $permissions = array();
    foreach ($rows as $row) {
      $permissions[$row->uid][$row->scheme] = TRUE;
    }
    return $permissions;
  }

  protected function loadSchemes() {
    return tac_lite_load_schemes();
  }

}
