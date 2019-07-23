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
    $rows = ceil($items/$cols);
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
          #TODO fix image url formatting, local return with _file (removing newline removes filename), external returns with local start
          $page['container_1']['row_' . $i]['col_'.$j] = array(
            '#type' => 'markup',
            '#prefix' => '<div class="views-col col-' . $j . '"><img src="' . (!preg_match('/(%40http)(s)?(%3A)/', $json['items'][$item_index]['image']) ? str_replace("_file", "file", $json['items'][$item_index]['image']) : 'https:' . preg_split('/(%40http)(s)?(%3A)/', $json['items'][$item_index]['image'])[1]) . '">',
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
  }

  /**
   * {@inheritdoc}
   * Associates order with json based on taxonomy
   */
  private function orderStaffProfiles($unsorted) {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('staff_profiles_order', 0, 1, TRUE);
    $sorted = array('items' => array());
    foreach ($terms as $term) {
      foreach ($unsorted['items'] as $num => $json) {
        if (($json['id'] == $term->get('field_spid')->value) && !empty($term->get('field_published')->value)) {
          $sorted['items'][] = $json;
        }
      }
    }
    return $sorted;
  }
}
