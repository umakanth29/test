<?php

namespace Drupal\fillpdf;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\fillpdf\Entity\FillPdfFormField;

/**
 * Class EntityHelper.
 *
 * @package Drupal\fillpdf
 */
class EntityHelper implements EntityHelperInterface {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;
  /**
   * Constructor.
   */
  public function __construct(QueryFactory $entity_query) {
    $this->entityQuery = $entity_query;
  }

  /**
   * @todo Move this to \Drupal\Entity\FillPdfForm itself and deprecate this method.
   */
  public function getFormFields(FillPdfFormInterface $fillpdf_form) {
    return FillPdfFormField::loadMultiple(
      $this->entityQuery->get('fillpdf_form_field')
        ->condition('fillpdf_form', $fillpdf_form->id())
        ->execute());
  }

}
