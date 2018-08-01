<?php

namespace Drupal\Tests\fillpdf\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the main FillPDF upload form.
 *
 * @group fillpdf
 */
class OverviewFormTest extends BrowserTestBase {

  static public $modules = ['fillpdf'];
  protected $profile = 'minimal';

  /**
   * Tests that upload form is accessible.
   */
  public function testUploadForm() {
    // Create and log in our privileged user.
    $user = $this->createUser([
      'access administration pages',
      'administer pdfs',
    ]);
    $this->drupalLogin($user);

    $this->drupalGet(Url::fromRoute('fillpdf.forms_admin'));
  }

}
