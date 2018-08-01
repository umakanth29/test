<?php
namespace Drupal\Tests\fillpdf\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Base class that can be inherited by FillPDF tests.
 */
abstract class FillPdfTestBase extends BrowserTestBase {

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;
  public static $modules = ['node', 'image', 'field_ui', 'image_module_test', 'fillpdf_test'];
  protected function setUp() {
    parent::setUp();

    // Create Basic page and Article node types.
    if ($this->profile != 'standard') {
      $this->drupalCreateContentType(['type' => 'page', 'name' => 'Basic page']);
      $this->drupalCreateContentType(['type' => 'article', 'name' => 'Article']);
    }

    $this->adminUser = $this->drupalCreateUser(['access content', 'access administration pages', 'administer site configuration', 'administer content types', 'administer node fields', 'administer nodes', 'create article content', 'edit any article content', 'delete any article content', 'administer image styles', 'administer node display']);
    $this->drupalLogin($this->adminUser);
  }

}
