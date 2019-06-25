<?php
namespace Drupal\staff_profile_feed\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Serialization;

/**
 * Provides page for Staff profile JSON feed
 */
class StaffProfilesList extends ControllerBase {
  /**
   * {@inheritdoc}
   * Generates table display
   */
  public function generatePage() {
    $json = StaffProfilesList::getStaffProfiles(TRUE);
    $items = count($json['items']);
    $cols = 2;
    $rows = $items/$cols;
    $page['container_1'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('views-view-grid', 'horizontal', 'cols-' . $cols),
      ),
    );
    $item_index = 0;
    for ($i=1; $i <= $rows; $i++) {
      $page['container_1']['row_' . $i] = array(
        '#type' => 'container',
        '#attributes' => array(
          'class' => array('views-row', 'row-' . $i),
        ),
      );
      for ($j=1; $j <= $cols; $j++) {
        if ($item_index < $items) {
          $page['container_1']['row_' . $i]['col_'.$j] = array(
            '#type' => 'markup',
            '#prefix' => '<div class="views-col col-' . $j . '">',
            '#markup' => $json['items'][$item_index]['content_html'],
            '#suffix' => '</div>',
            '#attributes' => array(
              'class' => array('views-col', 'col-' . $j),
            ),
          );
          $item_index += 1;
        }
      }
    }
    return $page;
  }

  /**
   * {@inheritdoc}
   * Loads and parses json
   */
  private function getStaffProfiles($ordered) {
    $config = \Drupal::config('staff_profile_feed.settings');
    $url = $config->get('staff_profile_json_url') . "/" . preg_replace('#[ -]+#', '-', $config->get('county_to_create_feed'));
    $json_str = file_get_contents($url);
    $decoded = json_decode($json_str, TRUE);
    if ($ordered) {
      return StaffProfilesList::orderStaffProfiles($decoded);
    }
    return $decoded;
    //TODO consider enabling caching to prevent error- will cause missing to be more persistant
    //TODO allow profile to be unpublished from json feed
  }

  /**
   * {@inheritdoc}
   * Associates order with json based on taxonomy
   */
  private function orderStaffProfiles($unsorted) {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('staff_profiles_order', 0, 1, TRUE);
    $sorted = array();
    foreach ($terms as $term) {
      foreach ($unsorted['items'] as $num => $json) {
        if ($json['id'] == $term->get('field_spid')->value) {
          $sorted['items'][] = $json;
        }
      }
    }
    return $sorted;
  }
}