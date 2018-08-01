<?php

namespace Drupal\fillpdf_test\Plugin\BackendService;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Drupal\fillpdf\Plugin\BackendServiceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @BackendService(
 *   id = "test",
 *   label = @Translation("FillPDF Test Backend Service"),
 * )
 */
class Test extends BackendServiceBase implements ContainerFactoryPluginInterface {

  /**
   * @var StateInterface
   */
  protected $state;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
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
   * {@inheritdoc}
   */
  public function parse($pdf) {
    return static::getParseResult();
  }

  /**
   * {@inheritdoc}
   */
  public function merge($pdf, array $field_mappings, array $options) {
    $this->state->set('fillpdf_test.last_populated_metadata', array(
      'field_mapping' => $field_mappings,
      'options' => $options,
    ));

    return file_get_contents(drupal_get_path('module', 'fillpdf_test') . '/files/fillpdf_test_v3.pdf');
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
