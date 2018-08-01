<?php

namespace Drupal\fillpdf\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fillpdf\FillPdfAdminFormHelperInterface;
use Drupal\fillpdf\FillPdfFormInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FillPdfFormFieldForm extends ContentEntityForm {

  protected $adminFormHelper;

  /**
   * The construct "magic method."
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   Injected service. Deprecated but still used in the parent class.
   * @param \Drupal\fillpdf\FillPdfAdminFormHelperInterface $admin_form_helper
   *   Injected service.
   */
  public function __construct(EntityManagerInterface $entity_manager, FillPdfAdminFormHelperInterface $admin_form_helper) {
    parent::__construct($entity_manager);
    $this->adminFormHelper = $admin_form_helper;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('fillpdf.admin_form_helper')
    );
  }

  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['token_help'] = $this->adminFormHelper->getAdminTokenForm();

    return $form;
  }

  public function save(array $form, FormStateInterface $form_state) {
    /** @var FillPdfFormInterface $entity */
    $entity = $this->entity;

    $form_state->setRedirect('entity.fillpdf_form.edit_form', [
      'fillpdf_form' => $this->entity->fillpdf_form->target_id,
    ]);

    return parent::save($form, $form_state);
  }

}
