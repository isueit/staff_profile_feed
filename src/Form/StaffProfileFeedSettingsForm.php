<?php
namespace Drupal\staff_profile_feed\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class StaffProfileFeedSettingsForm.
 */
class StaffProfileFeedSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'staff_profile_feed.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'staff_profile_feed_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $config = \Drupal::service('config.factory')->getEditable('staff_profile_feed.settings');
    $config->set('county_to_create_feed', $form_state->getValue('county'))
      ->set('staff_profile_json_url', $form_state->getValue('address'))
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('staff_profile_feed.settings');
    $form['county'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('County Name'),
      '#description' => $this->t('The county that staff are based at.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('county_to_create_feed'),
    );
    $form['address'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Feed Url'),
      '#description' => $this->t('The address that the staff profiles site uses for feeds. '),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('staff_profile_json_url'),
      //'#attributes' => array('disabled' => 'disabled'), #Disable to prevent users changing their feed
    );
    $url = $config->get('staff_profile_json_url') . "/" . preg_replace('#[ -]+#', '-', $config->get('county_to_create_feed'));
    $form['url'] = array(
      '#type' => 'markup',
      '#markup' => "Check to make sure this is your feed: " . $url,
    );

    return parent::buildForm($form, $form_state);
  }
}
