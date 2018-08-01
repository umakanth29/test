<?php

namespace Drupal\fillpdf\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fillpdf\EntityHelper;
use Drupal\fillpdf\SerializerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FillPdfFormImportForm extends EntityForm {

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
    $form = parent::form($form, $form_state);

    $form['paste'] = [
      '#type' => 'details',
      '#title' => $this->t('Paste code'),
      '#open' => TRUE,
    ];
    $form['paste']['code'] = [
      '#type' => 'textarea',
      '#default_value' => '',
      '#rows' => 30,
      '#description' => $this->t('Cut and paste the results of a <em>FillPDF configuration and mappings export</em> here.'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];

    return $form;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    unset($form['actions']);
    $form['#after_build'] = [];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $code = $form_state->getValue('code');
    $return = $this->serializer->deserializeForm($code);

    $fillpdf_form = $return['form'];
    $fields = $return['fields'];

    if (!is_object($fillpdf_form) || !count($fields)) {
      $form_state->setErrorByName('code', $this->t('There was a problem processing your FillPDF form code. Please do a fresh export from the source and try pasting it again.'));
    }
    else {
      $form_state->setValue('mappings', [
        'form' => $fillpdf_form,
        'fields' => $fields,
      ]);
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();

    /** @var \Drupal\fillpdf\FillPdfFormInterface $fillpdf_form */
    $fillpdf_form = $this->getEntity();

    $mappings = $form_state->getValue('mappings');

    /** @var \Drupal\fillpdf\FillPdfFormInterface $imported_form */
    $imported_form = $mappings['form'];

    /** @var array $imported_fields */
    $imported_fields = $mappings['fields'];

    $unmatched_pdf_keys = $this->serializer->importForm($fillpdf_form, $imported_form, $imported_fields);

    foreach ($unmatched_pdf_keys as $unmatched_pdf_key) {
      drupal_set_message($this->t('Your code contained field mappings for the PDF field key <em>@pdf_key</em>, but it does not exist on this form. Therefore, it was ignored.', ['@pdf_key' => $unmatched_pdf_key]), 'warning');
    }

    drupal_set_message($this->t('Successfully imported FillPDF form configuration and matching PDF field keys. If any field mappings failed to import, they are listed above.'));

    $form_state->setRedirect('entity.fillpdf_form.edit_form', ['fillpdf_form' => $fillpdf_form->id()]);
  }

}
