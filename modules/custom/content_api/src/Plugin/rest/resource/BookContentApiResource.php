<?php

namespace Drupal\content_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "book",
 *   label = @Translation("Book Resource"),
 *   uri_paths = {
 *     "canonical" = "/get/book"
 *   }
 * )
 */
class BookContentApiResource extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $response = [];
    $nid = \Drupal::entityQuery('node')
    ->condition('type', 'book')
    ->accessCheck(FALSE)
    ->execute();
    $node_id = reset($nid);

    if ($node_id) {
      $node = \Drupal\node\Entity\Node::load($node_id);
      $title = $node->get('field_book_title')->value;
      $description = $node->get('field_description')->value;
      $paragraph_field_items = $node->get('field_author')->getValue();

      foreach ($paragraph_field_items as $paragraph_item) {
        $paragraph = \Drupal\paragraphs\Entity\Paragraph::load($paragraph_item['target_id']);
        $paragraph_name_value = $paragraph->get('field_name')->value;
        $paragraph_bio_value = $paragraph->get('field_author_biography')->value;
        $generated_url_service = \Drupal::service('file_url_generator')->generateAbsoluteString($paragraph->field_image->entity->getFileUri());
        $response[] = [
          'title' => $title,
          'description' => $description,
          'paragraph_name_text' => $paragraph_name_value,
          'paragraph_bio_text' => $paragraph_bio_value,
          'paragraph_image_url' => $generated_url_service,
        ];
      }
    }
    return new ResourceResponse($response);
  }
}




