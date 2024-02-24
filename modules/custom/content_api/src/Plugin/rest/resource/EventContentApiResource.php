<?php

namespace Drupal\content_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use DateTime;

/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "event",
 *   label = @Translation("Event Resource"),
 *   uri_paths = {
 *     "canonical" = "/get/event"
 *   }
 * )
 */
class EventContentApiResource extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $response = [];
    $nid = \Drupal::entityQuery('node')
    ->condition('type', 'event')
    ->accessCheck(FALSE)
    ->execute();
    $node_id = reset($nid);

    if ($node_id) {
      $node = \Drupal\node\Entity\Node::load($node_id);
      $title = $node->get('field_event_title')->value;
      $description = $node->get('field_event_description')->value;

      $datetime_field = $node->get('field_date_and_time');

      if (!$datetime_field->isEmpty()) {
        $datetime = $datetime_field->value;
        $datetime_object = new DateTime($datetime);
        $formatted_datetime = $datetime_object->format("D, m/d/Y - H:i");
      } else {
        $formatted_datetime = 'No date and time available';
      }

      $location = $node->get('field_location')->value;
      $paragraph_field_items = $node->get('field_speaker')->getValue();

      foreach ($paragraph_field_items as $paragraph_item) {
        $paragraph = \Drupal\paragraphs\Entity\Paragraph::load($paragraph_item['target_id']);
        $speaker_name = $paragraph->get('field_speaker_name')->value;
        $speaker_bio = $paragraph->get('field_speaker_biography')->value;

        $response[] = [
          'title' => $title,
          'description' => $description,
          'datetime' => $formatted_datetime,
          'location' => $location,
          'speaker_name' => $speaker_name,
          'speaker_bio' => $speaker_bio,
        ];
      }
    }
    return new ResourceResponse($response);
  }
}




