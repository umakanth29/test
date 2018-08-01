<?php

namespace Drupal\fillpdf;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\file\FileInterface;
use Drupal\fillpdf\Entity\FillPdfForm;
use Drupal\fillpdf\Entity\FillPdfFormField;

/**
 * Class InputHelper.
 *
 * @package Drupal\fillpdf
 */
class InputHelper implements InputHelperInterface {

  /** @var \Drupal\Core\Config\ConfigFactoryInterface */
  protected $configManager;

  /**
   * @var \Drupal\fillpdf\FillPdfBackendManager
   */
  protected $backendManager;

  public function __construct(ConfigFactoryInterface $config_factory, FillPdfBackendManager $backend_manager) {
    $this->configManager = $config_factory;
    $this->backendManager = $backend_manager;
  }

  /**
   * @param \Drupal\file\FileInterface $file
   * @param \Drupal\fillpdf\FillPdfFormInterface $existing_form
   * @return array
   */
  public function attachPdfToForm(FileInterface $file, FillPdfFormInterface $existing_form = NULL) {
    $this->saveFileUpload($file);

    if ($existing_form) {
      $fillpdf_form = $existing_form;
      $fillpdf_form->file = $file;
    }
    else {
      $fillpdf_form = FillPdfForm::create([
        'file' => $file,
        'title' => $file->filename,
        'scheme' => $this->config('fillpdf.settings')->get('scheme'),
      ]);
    }

    // Save PDF configuration before parsing.
    $fillpdf_form->save();

    $config = $this->config('fillpdf.settings');
    $fillpdf_service = $config->get('backend');
    /** @var FillPdfBackendPluginInterface $backend */
    $backend = $this->backendManager->createInstance($fillpdf_service, $config->get());

    // Attempt to parse the fields in the PDF.
    $fields = $backend->parse($fillpdf_form);

    $form_fields = [];
    foreach ((array) $fields as $arr) {
      if ($arr['type']) { // Don't store "container" fields
        // pdftk sometimes inserts random &#0; markers - strip these out.
        // NOTE: This may break forms that actually DO contain this pattern,
        // but 99%-of-the-time functionality is better than merge failing due
        // to improper parsing.
        $arr['name'] = str_replace('&#0;', '', $arr['name']);
        $field = FillPdfFormField::create(
          [
            'fillpdf_form' => $fillpdf_form,
            'pdf_key' => $arr['name'],
            'value' => '',
          ]
        );

        $form_fields[] = $field;
      }
    }

    // Save the fields that were parsed out (if any). If none were, set a
    // warning message telling the user that.
    foreach ($form_fields as $fillpdf_form_field) {
      /** @var FillPdfFormField $fillpdf_form_field */
      $fillpdf_form_field->save();
    }
    return ['form' => $fillpdf_form, 'fields' => $form_fields];
  }

  /**
   * @param \Drupal\file\FileInterface $file
   */
  protected function saveFileUpload(FileInterface $file) {
    // Save the file to get an fid, and then create a FillPdfForm record
    // based off that.
    $file->setPermanent();
    // Save the file so we can get an fid
    $file->save();
  }

  protected function config($key) {
    return $this->configManager->get($key);
  }

}
