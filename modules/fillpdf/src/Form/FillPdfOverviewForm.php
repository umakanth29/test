<?php

namespace Drupal\fillpdf\Form;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\fillpdf\FillPdfBackendManager;
use Drupal\fillpdf\InputHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FillPdfOverviewForm extends FillPdfAdminFormBase {
  use FillPdfFormUploadTrait;

  /**
   * The backend manager (finds the filling plugin the user selected).
   *
   * @var FillPdfBackendManager
   */
  protected $backendManager;

  /** @var ModuleHandlerInterface $module_handler */
  protected $moduleHandler;

  /** @var AccountInterface $current_user */
  protected $currentUser;

  /** @var QueryFactory $entityQuery */
  protected $entityQuery;

  /** @var FileSystemInterface $fileSystem */
  protected $fileSystem;

  /** @var \Drupal\fillpdf\InputHelperInterface */
  protected $inputHelper;

  public function __construct(ModuleHandlerInterface $module_handler, FillPdfBackendManager $backend_manager, AccountInterface $current_user, QueryFactory $entity_query, FileSystemInterface $file_system, InputHelperInterface $input_helper) {
    parent::__construct();
    $this->backendManager = $backend_manager;
    $this->moduleHandler = $module_handler;
    $this->currentUser = $current_user;
    $this->entityQuery = $entity_query;
    $this->fileSystem = $file_system;
    $this->inputHelper = $input_helper;
  }

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      // Load the plugin manager.
      $container->get('plugin.manager.fillpdf_backend'),
      $container->get('current_user'),
      $container->get('entity.query'),
      $container->get('file_system'),
      $container->get('fillpdf.input_helper')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'fillpdf_forms_admin';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // @todo: convert to OOP
    $form['existing_forms'] = views_embed_view('fillpdf_forms', 'block_1');

    $config = $this->config('fillpdf.settings');
    // Only show PDF upload form if fillpdf is configured.
    if ($config->get('backend')) {
      // If using FillPDF Service, ensure XML-RPC module is present.
      if ($config->get('backend') !== 'fillpdf_service' || $this->moduleHandler->moduleExists('xmlrpc')) {
        $form['upload_pdf'] = [
          '#type' => 'file',
          '#title' => 'Upload',
          '#description' => $this->t('Upload a PDF template to create a new form'),
        ];

        $form['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Upload'),
          '#weight' => 15,
        ];
      }
      else {
        drupal_set_message($this->t('You must install the <a href=":xmlrpc">contributed XML-RPC module</a> in order to use FillPDF Service as your PDF-filling method.', [
          '@xmlrpc' => Url::fromUri('https://drupal.org/project/xmlrpc')
            ->toString(),
        ]), 'error');
      }
    }
    else {
      $form['message'] = [
        '#markup' => '<p>' . $this->t('Before you can upload PDF files, you must @link.', ['@link' => new FormattableMarkup($this->l($this->t('configure FillPDF'), Url::fromRoute('fillpdf.settings')), [])]) . '</p>',
      ];
      drupal_set_message($this->t('FillPDF is not configured.'), 'error');
    }
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $files = $this->getRequest()->files->get('files');

    $file_upload = array_key_exists('upload_pdf', $files) ? $files['upload_pdf'] : NULL;
    if ($file_upload) {
      $this->validatePdfUpload($form, $form_state, $file_upload);
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\file\FileInterface $file */
    $file = $form_state->getValue('upload_pdf');
    $added = $this->inputHelper->attachPdfToForm($file);

    $fillpdf_form = $added['form'];
    $form_fields = $added['fields'];

    if (count($form_fields) === 0) {
      drupal_set_message($this->t('No fields detected in PDF. Are you sure it contains editable fields?'), 'warning');
    }

    $fid = $fillpdf_form->id();
    $form_state->setRedirect('entity.fillpdf_form.edit_form', ['fillpdf_form' => $fid]);
  }

}
