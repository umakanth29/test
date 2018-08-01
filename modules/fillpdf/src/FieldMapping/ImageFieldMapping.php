<?php

namespace Drupal\fillpdf\FieldMapping;

use Drupal\fillpdf\FieldMapping;

class ImageFieldMapping extends FieldMapping {

  /**
   * @var string
   *
   * The common extension (jpg, png, or gif) corresponding to the type of image
   * data sent through.
   */
  protected $extension;

  /**
   * @param $data
   * @param $extension
   *
   * @throws \InvalidArgumentException
   */
  public function __construct($data, $extension) {
    parent::__construct($data);

    if (!in_array($extension, ['jpg', 'png', 'gif'])) {
      throw new \InvalidArgumentException('Extension must be one of: jpg, png, gif.');
    }
  }

  /**
   * @return string
   */
  public function getExtension() {
    return $this->extension;
  }

}
