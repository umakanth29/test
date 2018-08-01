<?php

namespace Drupal\Tests\fillpdf\Functional;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\fillpdf\Entity\FillPdfForm;
use Drupal\fillpdf\FieldMapping\ImageFieldMapping;
use Drupal\fillpdf\FieldMapping\TextFieldMapping;
use Drupal\fillpdf_test\Plugin\FillPdfBackend\TestFillPdfBackend;
use Drupal\node\Entity\Node;
use Drupal\simpletest\Tests\BrowserTest;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\fillpdf\Traits\TestFillPdfTrait;
use Drupal\Tests\image\Functional\ImageFieldTestBase;
use Drupal\Tests\image\Kernel\ImageFieldCreationTrait;
use Drupal\Tests\TestFileCreationTrait;
use Drupal\user\Entity\Role;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

/**
 * Tests entity and Webform image stamping.
 *
 * @group fillpdf
 */
class PdfPopulationTest extends FillPdfTestBase {

  use ImageFieldCreationTrait;
  use TestFileCreationTrait;
  use TestFillPdfTrait;

  protected $profile = 'minimal';
  protected $testNodeId;

  protected $contentType;

  /**
   * @var \Drupal\node\NodeInterface
   */
  protected $testNode;

  public function testPdfPopulation() {
    $this->createImageField('field_fillpdf_test_image', 'article');
    $files = $this->getTestFiles('image');
    $image = reset($files);
    $this->testNode = Node::load(
      $this->uploadNodeImage(
        $image,
        'field_fillpdf_test_image',
        'article',
        'FillPDF Test Image'
      )
    );

    // Test with a node.
    $this->uploadTestPdf();
    $fillpdf_form = FillPdfForm::load($this->getLatestFillPdfForm());

    // Get the field definitions for the form that was created and configure
    // them.
    $fields = $this->container->get('fillpdf.entity_helper')
      ->getFormFields($fillpdf_form);
    /** @var \Drupal\fillpdf\Entity\FillPdfFormField $field */
    foreach ($fields as $field) {
      switch ($field->pdf_key->value) {
        case 'ImageField':
          $field->value = '[node:field_fillpdf_test_image]';
          break;

        case 'TextField':
          $field->value = '[node:title]';
          break;
      }
      $field->save();
    }

    // Hit the generation route, check the results from the test backend plugin.
    $fillpdf_route = Url::fromRoute('fillpdf.populate_pdf', [], [
      'query' => [
        'fid' => $fillpdf_form->id(),
        'entity_id' => "node:{$this->testNode->id()}",
      ],
    ]);
    $this->drupalGet($fillpdf_route);

    // We don't actually care about downloading the fake PDF. We just want to
    // check what happened in the backend.
    $populate_result = $this->container->get('state')
      ->get('fillpdf_test.last_populated_metadata');

    self::assertEquals(
      $populate_result['field_mapping']['fields']['TextField'],
      $this->testNode->getTitle(),
      'PDF is populated with the title of the node.'
    );

    $node_file = File::load($this->testNode->field_fillpdf_test_image->target_id);
    self::assertEquals(
      $populate_result['field_mapping']['images']['ImageField']['data'],
      base64_encode(file_get_contents($node_file->getFileUri())),
      'Encoded image matches known image.'
    );

    $path_info = pathinfo($node_file->getFileUri());
    $expected_file_hash = md5($path_info['filename']) . '.' . $path_info['extension'];
    self::assertEquals(
      $populate_result['field_mapping']['images']['ImageField']['filenamehash'],
      $expected_file_hash,
      'Hashed filename matches known hash.'
    );
    self::assertEquals(
      $populate_result['field_mapping']['fields']['ImageField'],
      "{image}{$node_file->getFileUri()}",
      'URI in metadata matches expected URI.'
    );

    // Test with a Webform.
    $this->uploadTestPdf();
    $fillpdf_form2 = FillPdfForm::load($this->getLatestFillPdfForm());

    // Create a test submission for our Contact form.
    $contact_form = Webform::load('fillpdf_contact');
    $contact_form_test_route = Url::fromRoute('entity.webform.test_form', ['webform' => $contact_form->id()]);
    $this->drupalPostForm($contact_form_test_route, [], t('Send message'));

    // Load the submission.
    $submission = WebformSubmission::load($this->getLastSubmissionId($contact_form));

    $fillpdf_form2_fields = $this->container->get('fillpdf.entity_helper')
      ->getFormFields($fillpdf_form2);

    $expected_fields = TestFillPdfBackend::getParseResult();
    $expected_keys = [];
    $actual_keys = [];
    foreach ($fillpdf_form2_fields as $fillpdf_form2_field) {
      $actual_keys[] = $fillpdf_form2_field->pdf_key->value;
    }
    foreach ($expected_fields as $expected_field) {
      $expected_keys[] = $expected_field['name'];
    }
    // Sort the arrays before comparing.
    sort($expected_keys);
    sort($actual_keys);
    $differences = array_diff($expected_keys, $actual_keys);

    self::assertEmpty($differences, 'Parsed fields and fields in fixture match.');

    // Configure the fields for the next test.
    $fields = $fillpdf_form2_fields;
    /** @var \Drupal\fillpdf\Entity\FillPdfFormField $field */
    foreach ($fields as $field) {
      switch ($field->pdf_key->value) {
        case 'ImageField':
          $field->value = '[webform_submission:values:image]';
          break;

        case 'TextField':
          $field->value = '[webform_submission:webform:title]';
          break;
      }
      $field->save();
    }

    // Hit the generation route, check the results from the test backend plugin.
    $fillpdf_route = Url::fromRoute('fillpdf.populate_pdf', [], [
      'query' => [
        'fid' => $fillpdf_form2->id(),
        'entity_id' => "webform_submission:{$submission->id()}",
      ],
    ]);
    $this->drupalGet($fillpdf_route);

    // We don't actually care about downloading the fake PDF. We just want to
    // check what happened in the backend.
    $populate_result = $this->container->get('state')
      ->get('fillpdf_test.last_populated_metadata');

    $submission_values = $submission->getData();
    self::assertEquals(
      $populate_result['field_mapping']['fields']['TextField'],
      $submission->getWebform()->label(),
      'PDF is populated with the title of the Webform Submission.'
    );

    $submission_file = File::load($submission_values['image'][0]);
    self::assertEquals(
      $populate_result['field_mapping']['images']['ImageField']['data'],
      base64_encode(file_get_contents($submission_file->getFileUri())),
      'Encoded image matches known image.'
    );

    $path_info = pathinfo($submission_file->getFileUri());
    $expected_file_hash = md5($path_info['filename']) . '.' . $path_info['extension'];
    self::assertEquals(
      $populate_result['field_mapping']['images']['ImageField']['filenamehash'],
      $expected_file_hash,
      'Hashed filename matches known hash.'
    );

    self::assertEquals(
      $populate_result['field_mapping']['fields']['ImageField'],
      "{image}{$submission_file->getFileUri()}",
      'URI in metadata matches expected URI.'
    );

    // Test plugin APIs directly to make sure third-party consumers can use
    // them.
    $bsm = $this->container->get('plugin.manager.fillpdf_backend_service');
    /** @var \Drupal\fillpdf_test\Plugin\BackendService\Test $backend_service */
    $backend_service = $bsm->createInstance('test');

    // Test the parse method.
    $original_pdf = file_get_contents($this->getTestPdfPath());
    $parsed_fields = $backend_service->parse($original_pdf);
    $actual_keys = [];
    foreach ($parsed_fields as $parsed_field) {
      $actual_keys[] = $parsed_field['name'];
    }
    // Sort the arrays before comparing.
    sort($expected_keys);
    sort($actual_keys);
    $differences = array_diff($expected_keys, $actual_keys);

    self::assertEmpty($differences, 'Parsed fields from plugin and fields in fixture match.');

    // Test the merge method. We'd normally pass in values for $fields and
    // $options, but since this is a stub anyway, there isn't much point.
    // @todo: Test deeper using the State API.
    $merged_pdf = $backend_service->merge($original_pdf, [
      'Foo' => new TextFieldMapping('bar'),
      'Foo2' => new TextFieldMapping('bar2'),
      'Image1' => new ImageFieldMapping(file_get_contents($image->uri), 'png'),
    ], []);
    self::assertEquals($original_pdf, $merged_pdf);

    $merge_state = $this->container->get('state')
      ->get('fillpdf_test.last_populated_metadata');

    // Check that fields are set as expected.
    self::assertInstanceOf(TextFieldMapping::class, $merge_state['field_mapping']['Foo'], 'Field "Foo" was mapped to a TextFieldMapping object.');
    self::assertInstanceOf(TextFieldMapping::class, $merge_state['field_mapping']['Foo2'], 'Field "Foo2" was mapped to a TextFieldMapping object.');
    self::assertInstanceOf(ImageFieldMapping::class, $merge_state['field_mapping']['Image1'], 'Field "Image1" was mapped to an ImageFieldMapping object.');
  }

  /**
   * Get the last submission id.
   *
   * @param \Drupal\webform\WebformInterface $webform
   *
   * @return int
   *   The last submission id.
   */
  protected function getLastSubmissionId($webform) {
    // Get submission sid.
    $url = UrlHelper::parse($this->getUrl());
    if (isset($url['query']['sid'])) {
      return $url['query']['sid'];
    }

    $entity_ids = $this->container->get('entity_type.manager')
      ->getStorage('webform_submission')
      ->getQuery()
      ->sort('sid', 'DESC')
      ->condition('webform_id', $webform->id())
      ->execute();
    return reset($entity_ids);
  }

  protected function setUp() {
    parent::setUp();

    $this->configureBackend();

    // Add some roles to this user.
    $existing_user_roles = $this->adminUser->getRoles(TRUE);
    $role_to_modify = Role::load(end($existing_user_roles));

    // Grant additional permissions to this user.
    $this->grantPermissions($role_to_modify, [
      'access administration pages',
      'administer pdfs',
      'administer webform',
      'access webform submission log',
      'create webform',
    ]);
  }

}
