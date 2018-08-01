<?php

namespace Drupal\fillpdf\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\fillpdf\Component\Utility\FillPdf;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FillPdfFormUploadTrait
 * @package Drupal\fillpdf\Form
 */
trait FillPdfFormUploadTrait {

  protected function validatePdfUpload(array &$form, FormStateInterface &$form_state, UploadedFile $file_upload) {
    /**
     * @var $file_upload \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    if ($file_upload && $file_upload->isValid()) {
      // Move it to somewhere we know.
      $uploaded_filename = $file_upload->getClientOriginalName();

      // Ensure the destination is unique; we deliberately use managed files,
      // but they are keyed on file URI, so we can't save the same one twice.
      $scheme = $this->config('fillpdf.settings')->get('scheme');
      $destination = file_destination(FillPdf::buildFileUri($scheme, 'fillpdf/' . $uploaded_filename), FILE_EXISTS_RENAME);

      // Ensure our directory exists.
      $fillpdf_directory = FillPdf::buildFileUri($scheme, 'fillpdf');
      $directory_exists = file_prepare_directory($fillpdf_directory, FILE_CREATE_DIRECTORY + FILE_MODIFY_PERMISSIONS);

      if ($directory_exists) {
        $file_moved = $this->fileSystem->moveUploadedFile($file_upload->getRealPath(), $destination);

        if ($file_moved) {
          // Create a File object from the uploaded file.
          $new_file = File::create([
            'uri' => $destination,
            'uid' => $this->currentUser()->id(),
          ]);

          $errors = file_validate_extensions($new_file, 'pdf');

          if (count($errors)) {
            $form_state->setErrorByName('upload_pdf', $this->t('Only PDF files are supported, and they must end in .pdf.'));
          }
          else {
            $form_state->setValue('upload_pdf', $new_file);
          }
        }
        else {
          $form_state->setErrorByName('upload_pdf', $this->t("Could not move your uploaded file from PHP's temporary location to Drupal file storage."));
        }
      }
      else {
        $form_state->setErrorByName('upload_pdf', $this->t('Could not automatically create the <em>fillpdf</em> subdirectory. Please create this manually before uploading your PDF form.'));
      }
    }
    else {
      $form_state->setErrorByName('upload_pdf', $this->t('Your PDF could not be uploaded. Did you select one?'));
    }
  }

}
