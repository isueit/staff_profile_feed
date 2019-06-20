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
    $json = StaffProfilesList::getStaffProfiles();
    return array(
      '#type' => 'markup',
      '#markup' => serialize($json),
    );
    //$ordered = orderStaffProfiles($json);
  }

  /**
   * {@inheritdoc}
   * Loads and parses json
   */
  private function getStaffProfiles() {
    $config = \Drupal::config('staff_profile_feed');
    $url = $config->get('staff_profile_json_url') . "/" . $config->get('county_to_create_feed');
    $json_str = file_get_contents('http://localhost/drupal8entity/people/json-feed/Adair');
    $decoded = json_decode($json_str, TRUE);
    debug($decoded);
    return $decoded;
    //TODO consider enabling caching to prevent error- will cause error to be more persistant
  }

  /**
   * {@inheritdoc}
   * Associates order with json based on taxonomy
   */
  private function orderStaffProfiles($assoc_array) {

  }
}
