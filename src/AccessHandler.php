<?php

/**
 * @file
 * Contains \Drupal\tac_lite\AccessHandler.
 */

namespace Drupal\tac_lite;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccessHandler implements ContainerInjectionInterface {

  /**
   * @var EntityManagerInterface
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')
    );
  }

  /**
   * Gets terms from a node that belong to vocabularies selected for use by tac_lite
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node object
   *
   * @return array
   *   An array of term ids
   */
  public function getTermIds(NodeInterface $node) {
    $tids = array();

    /**
     * @todo: Inject tac_lite.settings
     */
    $vids = \Drupal::config('tac_lite.settings')->get('vocabularies');
    if ($vids) {
      // Load all terms found in term reference fields.
      // This logic should work for all nodes (published or not).
      $terms_by_vid = $this->getTermsByVocabulary($node);
      if (!empty($terms_by_vid)) {
        foreach ($vids as $vid) {
          if (!empty($terms_by_vid[$vid])) {
            foreach ($terms_by_vid[$vid] as $tid => $term) {
              $tids[$tid] = $tid;
            }
          }
        }
      }
    }

    return $tids;
  }

  /**
   * In Drupal 6.x, there was taxonomy_node_get_terms().  Drupal 7.x should
   * provide the same feature, but doesn't.  Here is our workaround, based on
   * https://drupal.org/comment/5573176#comment-5573176.
   *
   * We organize our data structure by vid and tid.
   */
  public function getTermsByVocabulary(NodeInterface $node) {
    $tids = array();

    foreach ($node->getFieldDefinitions() as $field) {
      $field_name = $field->getName();
      if ($field->getType() == 'taxonomy_term_reference') {
        foreach ($node->getTranslationLanguages() as $language) {
          foreach ($node->getTranslation($language->getId())->$field_name as $item) {
            if (!$item->isEmpty()) {
              $term = $item->entity;
              if ($term) {
                $tid = $term->id();
                $bundle = $term->bundle();
                $tids[$bundle][$tid] = $tid;
              }
            }
          }
        }
      }
    }

    return $tids;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeAccessRecords(NodeInterface $node) {
    // Get the tids we care about that are assigned to this node
    $tids = $this->getTermIds($node);
    $grants = array();

    if ($tids) {
      $schemes = $this->entityManager->getStorage('tac_lite_scheme')->loadMultiple();
      foreach ($schemes as $scheme) {
        foreach ($tids as $tid) {
          $grant = array(
            'realm' => $scheme->realm(),
            'gid' => $tid,
            'grant_view' => 0,
            'grant_update' => 0,
            'grant_delete' => 0,
            'priority' => 0,
          );
          foreach ($scheme->permissions as $permission => $value) {
            $grant[$permission] = (int) !empty($value);
          }
          $grants[] = $grant;
        }
      }
    }

    return $grants;
  }

}
