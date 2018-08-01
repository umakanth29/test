<?php

namespace Drupal\fillpdf\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fillpdf\EntityHelper;
use Drupal\fillpdf\FillPdfFormInterface;
use Drupal\fillpdf\SerializerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FillPdfFormExportForm extends EntityForm {

  /** @var \Drupal\fillpdf\SerializerInterface */
  protected $serializer;

  /** @var \Drupal\fillpdf\EntityHelper */
  protected $entityHelper;

  public function __construct(SerializerInterface $serializer, EntityHelper $entity_helper) {
    $this->serializer = $serializer;
    $this->entityHelper = $entity_helper;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('fillpdf.serializer'), $container->get('fillpdf.entity_helper'));
  }

  public function form(array $form, FormStateInterface $form_state) {
    parent::form($form, $form_state);

    /** @var FillPdfFormInterface $entity */
    $entity = $this->getEntity();

    $code = $this->serializer->getFormExportCode($entity);

    $form = array();
    $form['export'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('FillPDF form configuration and mappings'),
      '#default_value' => $code,
      '#rows' => 30,
      '#description' => $this->t('Copy this code and then on the site you want to import to, go to the Edit page for the FillPDF form for which you want to import these mappings, and paste it in there.'),
      '#attributes' => array(
        'style' => 'width: 97%;',
      ),
    );

    return $form;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    unset($form['actions']);
    $form['#after_build'] = [];
    return $form;
  }

}
