<?php

namespace Drupal\fillpdf\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\fillpdf\FillPdfContextManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FillPdfContextManager implements FillPdfContextManagerInterface {

  /** @var EntityTypeManagerInterface $entityTypeManager */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'));
  }

  /**
   * {@inheritDoc}
   */
  public function loadEntities(array $context) {
    $entities = [];

    foreach ($context['entity_ids'] as $entity_type => $entity_ids) {
      $type_controller = $this->entityTypeManager->getStorage($entity_type);
      $entity_list = $type_controller->loadMultiple($entity_ids);

      if (!empty($entity_list)) {
        // Initialize array.
        $entities += [$entity_type => []];
        $entities[$entity_type] += $entity_list;
      }
    }

    return $entities;
  }

}
