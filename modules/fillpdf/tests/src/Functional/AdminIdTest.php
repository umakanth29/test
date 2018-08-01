<?php

namespace Drupal\Tests\fillpdf\Functional;

use Drupal\fillpdf\Entity\FillPdfForm;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\fillpdf\Traits\TestFillPdfTrait;

/**
 * Ensure Edit links for PDFs at /admin/structure/fillpdf function correctly.
 *
 * @group fillpdf
 */
class AdminIdTest extends BrowserTestBase {

  use TestFillPdfTrait;

  static public $modules = ['fillpdf_test'];
  protected $profile = 'minimal';

  public function testEditLink() {
    $this->uploadTestPdf();
    $latest_fid = $this->getLatestFillPdfForm();
    $latest_fillpdf_form = FillPdfForm::load($latest_fid);
    $max_fid_after = $latest_fillpdf_form->fid->value;
    $this->drupalGet('admin/structure/fillpdf/' . $max_fid_after);
    $this->assertSession()->statusCodeEquals(200);
  }

  protected function setUp() {
    parent::setUp();

    $this->configureBackend();
    $this->initializeUser();
  }

}
