<?php

namespace Drupal\content_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\GeneratedUrl;

/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "product",
 *   label = @Translation("Product Resource"),
 *   uri_paths = {
 *     "canonical" = "/get/product"
 *   }
 * )
 */
class ProductContentApiResource extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $response = [];
    $nid = \Drupal::entityQuery('node')
    ->condition('type', 'product')
    ->accessCheck(FALSE)
    ->execute();
    $node_id = reset($nid);
    if ($node_id) {
      $node = \Drupal\node\Entity\Node::load($node_id);
      $title = $node->get('field_product_title')->value;
      $manufacturer = $node->get('field_manufacturer')->value;
      $paragraph_field_items = $node->get('field_review')->getValue();

      foreach ($paragraph_field_items as $paragraph_item) {
        $paragraph = \Drupal\paragraphs\Entity\Paragraph::load($paragraph_item['target_id']);
        $rating = $paragraph->get('field_rating')->value;
        $comments = $paragraph->get('field_comments')->value;

        $response[] = [
          'title' => $title,
          'manufacturer' => $manufacturer,
          'rating' => $rating ,
          'comments' => $comments,
        ];
      }
    }
    return new ResourceResponse($response);
  }
}




