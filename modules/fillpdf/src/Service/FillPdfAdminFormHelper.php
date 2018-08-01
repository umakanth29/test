<?php

namespace Drupal\fillpdf\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\fillpdf\FillPdfAdminFormHelperInterface;

class FillPdfAdminFormHelper implements FillPdfAdminFormHelperInterface {

  /** @var ModuleHandlerInterface $module_handler */
  protected $moduleHandler;

  /** @var \Drupal\Core\Config\ConfigFactoryInterface */
  protected $configFactory;

  public function __construct(ModuleHandlerInterface $module_handler, ConfigFactoryInterface $config_factory) {
    $this->moduleHandler = $module_handler;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getAdminTokenForm() {
    if (function_exists('template_preprocess_token_tree')) {
      $theme_function = 'token_tree';
    }
    else {
      $theme_function = 'token_tree_link';
    }

    return [
      '#theme' => $theme_function,
      '#token_types' => 'all',
      '#global_types' => TRUE,
    ];
  }

  /**
   * Returns acceptable file scheme options.
   *
   * Suitable for use with FAPI radio buttons.
   *
   * @return array
   */
  public static function schemeOptions() {
    return [
      'private' => t('Private files'),
      'public' => t('Public files'),
    ];
  }

  public static function getReplacementsDescription() {
    return t("<p>Tokens, such as those from fields, sometimes output values that need additional
  processing prior to being sent to the PDF. A common example is when a key within a field's <em>Allowed values</em>
  configuration does not match the field name or option value in the PDF that you would like to be selected but you
  do not want to change the <em>Allowed values</em> key.</p><p>This field will replace any matching values with the
  replacements you specify. Specify <strong>one replacement per line</strong> in the format
  <em>original value|replacement value</em>. For example, <em>yes|Y</em> will fill the PDF with
  <strong><em>Y</em></strong> anywhere that <strong><em>yes</em></strong> would have originally
  been used. <p>Note that omitting the <em>replacement value</em> will replace <em>original value</em>
  with a blank, essentially erasing it.</p>");
  }

  public function getPdftkPath() {
    $path_to_pdftk = $this->configFactory->get('fillpdf.settings')
      ->get('pdftk_path');

    if (empty($path_to_pdftk)) {
      $path_to_pdftk = 'pdftk';
      return $path_to_pdftk;
    }
    return $path_to_pdftk;
  }

}
