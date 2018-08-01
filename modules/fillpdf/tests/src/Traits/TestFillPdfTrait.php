<?php

namespace Drupal\Tests\fillpdf\Traits;

use Drupal\node\NodeInterface;

trait TestFillPdfTrait {

  protected function configureBackend() {
    // FillPDF needs to be configured.
    /** @var \Drupal\Core\Config\Config $fillpdf_settings */
    $fillpdf_settings = $this->container->get('config.factory')
      ->getEditable('fillpdf.settings')
      ->set('scheme', 'public')
      ->set('backend', 'test');
    $fillpdf_settings->save();
  }

  protected function initializeUser() {
    // Create and log in our privileged user.
    $account = $this->drupalCreateUser([
      'access administration pages',
      'administer pdfs',
    ]);
    $this->drupalLogin($account);
  }

  protected function uploadTestPdf() {
    // Upload a test file.
    $edit = array(
      'files[upload_pdf]' => $this->getTestPdfPath(),
    );
    $this->drupalPostForm('admin/structure/fillpdf', $edit, 'Upload');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * @return mixed
   */
  protected function getLatestFillPdfForm() {
    // Get the fid of the uploaded file to construct the link.
    $entity_query = $this->container->get('entity_type.manager')
      ->getStorage('fillpdf_form')
      ->getQuery();
    $max_fid_after_result = $entity_query
      ->sort('fid', 'DESC')
      ->range(0, 1)
      ->execute();
    return reset($max_fid_after_result);
  }

  /**
   * @param $file_system
   * @return mixed
   */
  protected function getTestPdfPath() {
    /** @var \Drupal\Core\File\FileSystem $file_system */
    $file_system = $this->container->get('file_system');
    return $file_system->realpath(drupal_get_path('module', 'fillpdf') . '/tests/modules/fillpdf_test/files/fillpdf_test_v3.pdf');
  }

  /**
   * Upload an image to a node.
   *
   * This is a fixed version of this method. The one from core is currently
   * broken.
   *
   * @todo: Keep an eye on https://www.drupal.org/project/drupal/issues/2863626
   * and consider switching back to that when it's done.
   *
   * @see \Drupal\Tests\image\Functional\ImageFieldTestBase::uploadNodeImage()
   *
   * @param $image
   *   A file object representing the image to upload.
   * @param $field_name
   *   Name of the image field the image should be attached to.
   * @param $type
   *   The type of node to create.
   * @param $alt
   *   The alt text for the image. Use if the field settings require alt text.
   */
  public function uploadNodeImage($image, $field_name, $type, $alt = '') {
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
    ];
    $edit['files[' . $field_name . '_0]'] = drupal_realpath($image->uri);
    $edit['status[value]'] = NodeInterface::PUBLISHED;
    $this->drupalPostForm('node/add/' . $type, $edit, t('Save'));
    if ($alt) {
      // Add alt text.
      $this->drupalPostForm(NULL, [$field_name . '[0][alt]' => $alt], t('Save'));
    }

    // Retrieve ID of the newly created node from the current URL.
    $matches = [];
    preg_match('/node\/([0-9]+)/', $this->getUrl(), $matches);
    return isset($matches[1]) ? $matches[1] : FALSE;
  }

}
