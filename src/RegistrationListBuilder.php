<?php

/**
 * @file
 * Contains \Drupal\rng\RegistrationListBuilder.
 */

namespace Drupal\rng;

use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Builds a list of registrations.
 */
class RegistrationListBuilder extends EntityListBuilder {
  /**
   * Row Counter.
   *
   * @var integer
   */
  protected $row_counter;

  /**
   * The event entity.
   *
   * @var EntityInterface
   */
  protected $event;

  /**
   * {@inheritdoc}
   *
   * @param EntityInterface $event
   *   The event entity to display registrations.
   */
  public function render(EntityInterface $event = NULL) {
    if (isset($event)) {
      $this->event = $event;
    }
    $render = parent::render();
    $render['#empty'] = t('No registrations found for this event.');
    return $render;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    if (isset($this->event)) {
      $registration_ids = \Drupal::entityQuery('registration')
        ->condition('event', $this->event->getEntityTypeId() . ':' . $this->event->id() , '=')
        ->execute();
      return entity_load_multiple('registration', $registration_ids);
    }
    return parent::load();
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    if ($entity->access('view') && $entity->hasLinkTemplate('canonical')) {
      $operations['view'] = array(
        'title' => $this->t('View'),
        'weight' => 0,
        'url' => $entity->urlInfo('canonical'),
      );
    }
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['counter'] = '';
    $header['type'] = $this->t('Type');
    $header['created'] = $this->t('Created');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['counter'] = ++$this->row_counter;
    $row['type'] = $entity->type->entity->label();
    $row['created'] = format_date($entity->created->value);
    return $row + parent::buildRow($entity);
  }
}