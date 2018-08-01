<?php

namespace Drupal\fillpdf;

/**
 * Interface SerializerInterface.
 *
 * @package Drupal\fillpdf
 *
 * @todo: Document
 */
interface SerializerInterface {

  public function getFormExportCode(FillPdfFormInterface $fillpdf_form);

  public function deserializeForm($code);

  public function importForm(FillPdfFormInterface $fillpdf_form, FillPdfFormInterface $imported_form, array $imported_fields);

  /**
   * Overwrite empty field values imported from export code with previous
   * existing values.
   *
   * @param array $keyed_fields
   * An array of unsaved FillPdfFormFieldInterface
   * objects keyed by PDF key.
   *
   * @param array $existing_fields
   * @return array
   */
  public function importFormFields(array $keyed_fields, array $existing_fields = []);

  /**
   * Overwrite empty new field values with previous existing values.
   *
   * @param array $form_fields
   * An array of saved FillPdfFormFieldInterface objects indexed by entity ID.
   *
   * @param array $existing_fields
   * @return array
   *
   * @see \Drupal\fillpdf\SerializerInterface::importFormFields()
   */
  public function importFormFieldsByKey(array $form_fields, array $existing_fields = []);

}
