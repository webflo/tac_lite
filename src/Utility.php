<?php

/**
 * @file
 * Contains \Drupal\tac_lite\Utility.
 */

namespace Drupal\tac_lite;

class Utility {

  public static function grantPermission($scheme, $uid, $tid) {
    return db_merge('tac_lite_user')
      ->keys(array('scheme' => $scheme, 'uid' => $uid, 'tid' => $tid))
      ->fields(array('scheme' => $scheme, 'uid' => $uid, 'tid' => $tid))
      ->execute();
  }

  public static function revokePermission($scheme, $uid, $tid) {
    return db_delete('tac_lite_user')
      ->condition('scheme', $scheme)
      ->condition('uid', $uid)
      ->condition('tid', $tid)
      ->execute();
  }


}
