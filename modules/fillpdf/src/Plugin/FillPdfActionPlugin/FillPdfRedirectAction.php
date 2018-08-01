<?php

namespace Drupal\fillpdf\Plugin\FillPdfActionPlugin;

use Drupal\Core\Annotation\Translation;
use Drupal\fillpdf\Annotation\FillPdfActionPlugin;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FillPdfRedirectAction
 * @package Drupal\fillpdf\Plugin\FillPdfActionPlugin
 *
 * @FillPdfActionPlugin(
 *   id = "redirect",
 *   label = @Translation("Redirect PDF to file")
 * )
 */
class FillPdfRedirectAction extends FillPdfSaveAction {

  public function execute() {
    $saved_file = $this->savePdf();

    // Get file URI, then return a RedirectResponse to it.
    $destination = $saved_file->url();

    return new RedirectResponse($destination);
  }

}
