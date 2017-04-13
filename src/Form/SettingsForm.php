<?php

namespace Drupal\visitor_tracking_inbound_code\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'visitor_tracking_inbound_code_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('visitor_tracking_inbound_code.settings')
      ->set('request_keys', $form_state->getValue('request_keys'))
      ->set('tracking_fields', $form_state->getValue('tracking_fields'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['visitor_tracking_inbound_code.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('visitor_tracking_inbound_code.settings');

    $form['description'] = [
      '#markup' => '<p>Note: This form is temporary. It will eventually be replaced when the Visitor Tracking module is stable.</p>'
    ];

    $form['request_keys'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Request keys'),
      '#description' => $this->t('A comma-separated list of request keys to track for visitors when they are present, optionally with a title after separated with a colon.'),
      '#default_value' => $config->get('request_keys'),
    ];

    $form['tracking_fields'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Tracking fields'),
      '#description' => $this->t('A comma-separated list of field names to track, optionally preceded with an entity type separated with a colon.'),
      '#default_value' => $config->get('tracking_fields'),
    ];

    return parent::buildForm($form, $form_state);
  }
}
