<?php
/**
 * @file
 * Contains \Drupal\scrollprogress_page\Form\WorkForm.
 */
namespace Drupal\scrollprogress_page\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ScrollProgressSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scrollprogress_page_form';
  }

  /**
   * return object
   *  each type of content object
   */
  public function getAllContentType() {
    return \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Prepare scrollprogress.settings
    $config = \Drupal::config('scrollprogress.settings');
    // array to convert activ scrolprogress to open fieldset
    $open = array(
      0 => FALSE,
      1 => TRUE
    );
    // process for for each type of content
    foreach ($this->getAllContentType() as $contentType) {

      $form['#tree'] = TRUE;
      
      // Open the fieldset if activ
      $default_activ = 'spp_'.$contentType->id().'_activ';
      $form[$contentType->id()] = [
        '#type' => 'details',
        '#title' => $contentType->label(),
        '#open' => ($config->get($default_activ)) ? $open[$config->get($default_activ)] : FALSE,
        '#prefix' => '<div class="scroll-process-form-wrapper scroll-process-details">',
        '#suffix' => '</div>',
      ];

      // Active
      $form[$contentType->id()]['activ_progress'] = [
        '#type' => 'radios',
        '#title' => $this->t('Active '),
        '#options' => [
          0 => $this->t('Off'),
          1 => $this->t('On'),
        ],
        '#default_value' => ($config->get($default_activ)) ? $config->get($default_activ) : 0,
        '#prefix' => '<div class="scroll-process-form scroll-process-activ">',
        '#suffix' => '</div>',
      ];

      // Div
      $default_div = 'spp_'.$contentType->id().'_div';
      $form[$contentType->id()]['div_content'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Div'),
        '#description' => $this->t('Choose a #id or a .class to attach the scrollprogress page.'),
        '#size' => 60,
        '#maxlength' => 128,
        '#default_value' => ($config->get($default_div)) ? $config->get($default_div) : '.content',
        '#prefix' => '<div class="scroll-process-form scroll-process-div">',
        '#suffix' => '</div>',
      ];

      // Color progress
      $default_color = 'spp_'.$contentType->id().'_color';
      $form[$contentType->id()]['color'] = [
        '#type' => 'color',
        '#title' => $this->t('Color'),
        '#description' => $this->t('Choose a color for the progress indicator.'),
        '#default_value' => ($config->get($default_color)) ? $config->get($default_color) : '#ffffff',
        '#prefix' => '<div class="scroll-process-form scroll-process-color">',
        '#suffix' => '</div>',
      ];

    }

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );

    $form['#attached']['library'][] = 'scrollprogress_page/scrollprogressadmin';

    return $form;

  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {

    foreach ($this->getAllContentType() as $contentType) {
      $contentTypesList[] = $contentType->id();
    }

    foreach ($form_state->getValues() as $ctype => $tc) {
      if(in_array($ctype, $contentTypesList)){
        $config = \Drupal::service('config.factory')->getEditable('scrollprogress.settings');
        $config->set('spp_'.$ctype.'_activ', $tc['activ_progress'])
          ->set('spp_'.$ctype.'_div', $tc['div_content'])
          ->set('spp_'.$ctype.'_color', $tc['color'])
          ->save();
      }
    }
  }

}
