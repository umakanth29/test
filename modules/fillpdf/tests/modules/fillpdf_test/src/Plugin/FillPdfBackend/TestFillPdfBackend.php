<?php
/**
 * @file
 * Contains \Drupal\fillpdf_test\Plugin\FillPdfBackend\TestFillPdfBackend.
 */

namespace Drupal\fillpdf_test\Plugin\FillPdfBackend;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Drupal\fillpdf\FillPdfBackendPluginInterface;
use Drupal\fillpdf\FillPdfFormInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Plugin(
 *   id = "test",
 *   label = @Translation("Pass-through plugin for testing")
 * )
 */
class TestFillPdfBackend implements FillPdfBackendPluginInterface, ContainerFactoryPluginInterface {

  /** @var array $configuration */
  protected $configuration;

  /**
   * @var StateInterface
   */
  protected $state;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, StateInterface $state) {
    $this->configuration = $configuration;
    $this->state = $state;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('state')
    );
  }

  /**
   * @inheritdoc
   */
  public function parse(FillPdfFormInterface $fillpdf_form) {
    return static::getParseResult();
  }

  /**
   * @inheritdoc
   */
  public function populateWithFieldData(FillPdfFormInterface $pdf_form, array $field_mapping, array $context) {
    // Not really populated, but that isn't our job.
    $populated_pdf = file_get_contents(drupal_get_path('module', 'fillpdf_test') . '/files/fillpdf_test_v3.pdf');

    $this->state->set('fillpdf_test.last_populated_metadata', [
      'field_mapping' => $field_mapping,
      'context' => $context,
    ]);

    return $populated_pdf;
  }

  public static function getParseResult() {
    return [
      0 => [
        'name' => 'ImageField',
        'value' => '',
        'type' => 'Pushbutton',
      ],
      1 => [
        'name' => 'Button',
        'value' => '',
        'type' => 'Pushbutton',
      ],
      2 => [
        'name' => 'TextField',
        'value' => '',
        'type' => 'Text',
      ],
    ];
  }

}
