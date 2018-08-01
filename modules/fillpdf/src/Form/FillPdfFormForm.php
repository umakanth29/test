<?php

namespace Drupal\fillpdf\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\fillpdf\Component\Utility\FillPdf;
use Drupal\fillpdf\EntityHelper;
use Drupal\fillpdf\FillPdfAdminFormHelperInterface;
use Drupal\fillpdf\FillPdfFormFieldInterface;
use Drupal\fillpdf\FillPdfFormInterface;
use Drupal\fillpdf\FillPdfLinkManipulatorInterface;
use Drupal\fillpdf\InputHelperInterface;
use Drupal\fillpdf\SerializerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FillPdfFormForm extends ContentEntityForm {
  use FillPdfFormUploadTrait;

  /** @var \Drupal\fillpdf\FillPdfAdminFormHelperInterface */
  protected $adminFormHelper;

  /** @var \Drupal\fillpdf\FillPdfLinkManipulatorInterface */
  protected $linkManipulator;

  /** @var \Drupal\fillpdf\EntityHelper */
  protected $entityHelper;

  /** @var \Drupal\fillpdf\InputHelperInterface */
  protected $inputHelper;

  /** @var \Drupal\fillpdf\SerializerInterface */
  protected $serializer;

  /** @var \Drupal\Core\File\FileSystemInterface */
  protected $fileSystem;

  public function __construct(EntityManagerInterface $entity_manager, FillPdfAdminFormHelperInterface $admin_form_helper,
                              FillPdfLinkManipulatorInterface $link_manipulator,
                              EntityHelper $entity_helper, InputHelperInterface $input_helper,
                              SerializerInterface $fillpdf_serializer, FileSystemInterface $file_system) {
    $this->entityManager = $entity_manager;
    parent::__construct($this->entityManager);
    $this->adminFormHelper = $admin_form_helper;
    $this->linkManipulator = $link_manipulator;
    $this->entityHelper = $entity_helper;
    $this->inputHelper = $input_helper;
    $this->serializer = $fillpdf_serializer;
    $this->fileSystem = $file_system;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('fillpdf.admin_form_helper'),
      $container->get('fillpdf.link_manipulator'),
      $container->get('fillpdf.entity_helper'),
      $container->get('fillpdf.input_helper'),
      $container->get('fillpdf.serializer'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var FillPdfFormInterface $entity */
    $entity = $this->entity;

    $form['tokens'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Tokens'),
      '#weight' => 11,
      'token_tree' => $this->adminFormHelper->getAdminTokenForm(),
    ];

    $entity_types = [];
    $entity_type_definitions = $this->entityManager->getDefinitions();

    foreach ($entity_type_definitions as $machine_name => $definition) {
      $label = $definition->getLabel();
      $entity_types[$machine_name] = "$machine_name ($label)";
    }

    // @todo: Encapsulate this logic into a ::getDefaultEntityType() method on FillPdfForm
    $field_default_entity_type = $entity->get('default_entity_type');
    $default_entity_type = count($field_default_entity_type) ? $field_default_entity_type->first()->value : NULL;
    if (empty($default_entity_type)) {
      $default_entity_type = 'node';
    }

    $form['default_entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Default entity type'),
      '#options' => $entity_types,
      '#weight' => 12.5,
      '#default_value' => $default_entity_type,
    ];

    $fid = $entity->id();

    /** @var FileInterface $file_entity */
    $file_entity = File::load($entity->get('file')->first()->target_id);
    $pdf_info_weight = 0;
    $form['pdf_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('PDF form information'),
      '#weight' => $form['default_entity_id']['#weight'] + 1,
      'submitted_pdf' => [
        '#type' => 'item',
        '#title' => $this->t('Uploaded PDF'),
        '#description' => $file_entity->getFileUri(),
        '#weight' => $pdf_info_weight++,
      ],
      'upload_pdf' => [
        '#type' => 'file',
        '#title' => 'Update PDF template',
        '#description' => $this->t('Update the PDF template used by this form'),
        '#weight' => $pdf_info_weight++,
      ],
      'sample_populate' => [
        '#type' => 'item',
        '#title' => 'Sample PDF',
        '#description' => $this->l($this->t('See which fields are which in this PDF.'),
            $this->linkManipulator->generateLink([
              'fid' => $fid,
              'sample' => TRUE,
            ])) . '<br />' .
          $this->t('If you have set a custom path on this PDF, the sample will be saved there silently.'),
        '#weight' => $pdf_info_weight++,
      ],
      'form_id' => [
        '#type' => 'item',
        '#title' => 'Form Info',
        '#description' => $this->t("Form ID: [@fid].  Populate this form with entity IDs, such as /fillpdf?fid=$fid&entity_type=node&entity_id=10<br/>", ['@fid' => $fid]),
        '#weight' => $pdf_info_weight,
      ],
    ];

    if (!empty($entity->get('default_entity_id')->first()->value)) {
      $parameters = [
        'fid' => $fid,
      ];
      $form['pdf_info']['populate_default'] = [
        '#type' => 'item',
        '#title' => 'Fill PDF from default node',
        '#description' => $this->l($this->t('Download this PDF filled with data from the default entity (@entity_type:@entity).',
            [
              '@entity_type' => $entity->default_entity_type->value,
              '@entity' => $entity->default_entity_id->value,
            ]
          ),
            $this->linkManipulator->generateLink($parameters)) . '<br />' .
          $this->t('If you have set a custom path on this PDF, the sample will be saved there silently.'),
        '#weight' => $form['pdf_info']['form_id']['#weight'] - 0.1,
      ];
    }

    $additional_setting_set = $entity->destination_path->value || $entity->destination_redirect->value;
    $form['additional_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Additional settings'),
      '#weight' => $form['pdf_info']['#weight'] + 1,
      '#open' => $additional_setting_set,
    ];

    $form['destination_path']['#group'] = 'additional_settings';
    $form['scheme']['#group'] = 'additional_settings';
    $form['destination_redirect']['#group'] = 'additional_settings';
    $form['replacements']['#group'] = 'additional_settings';
    $form['replacements']['#weight'] = 1;

    // @todo: Add a button to let them attempt re-parsing if it failed.
    $form['fillpdf_fields']['fields'] = FillPdf::embedView('fillpdf_form_fields',
      'block_1',
      $entity->id());

    $form['fillpdf_fields']['#weight'] = 100;

    $form['export_fields'] = [
      '#prefix' => '<div>',
      '#markup' => $this->l($this->t('Export these field mappings'), Url::fromRoute('entity.fillpdf_form.export_form', ['fillpdf_form' => $entity->id()])),
      '#suffix' => '</div>',
      '#weight' => 100,
    ];

    $form['import_fields'] = [
      '#prefix' => '<div>',
      '#markup' => $this->l($this->t('Import a previous export into this PDF'), Url::fromRoute('entity.fillpdf_form.import_form', ['fillpdf_form' => $entity->id()])),
      '#suffix' => '</div>',
      '#weight' => 100,
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $files = $this->getRequest()->files->get('files');

    /** @var UploadedFile|null $file_upload */
    $file_upload = array_key_exists('upload_pdf', $files) ? $files['upload_pdf'] : NULL;
    if ($file_upload) {
      $this->validatePdfUpload($form, $form_state, $file_upload);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var FillPdfFormInterface $entity */
    $entity = $this->getEntity();

    /** @var \Drupal\file\FileInterface $file */
    $file = $form_state->getValue('upload_pdf');

    if ($file) {
      $existing_fields = $this->entityHelper->getFormFields($entity);

      // Delete existing fields.
      /** @var FillPdfFormFieldInterface $existing_field */
      foreach ($existing_fields as $existing_field) {
        $existing_field->delete();
      }

      $added = $this->inputHelper->attachPdfToForm($file, $entity);

      $form_fields = $added['fields'];

      // Import previous form field values over new fields.
      $non_matching_msg = '';
      $non_matching_fields = $this->serializer->importFormFieldsByKey($existing_fields, $form_fields);
      if (count($non_matching_fields)) {
        $non_matching_msg = $this->t(" These keys couldn't be found in the new PDF");
      }

      drupal_set_message($this->t("Your previous field mappings have been transferred to the new PDF template you uploaded.") . $non_matching_msg);

      foreach ($non_matching_fields as $non_matching_field) {
        drupal_set_message($non_matching_field, 'warning');
      }

      drupal_set_message($this->t('You might also want to update the <em>Filename pattern</em> field; this has not been changed.'));
    }

    $entity->set('default_entity_type', $form_state->getValue('default_entity_type'));

    $entity->save();
  }

}
