<?php

/**
 * @file
 * Contains \Drupal\rng\Form\RegistrationForm.
 */

namespace Drupal\rng\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for registrations.
 */
class RegistrationForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $registration = $this->getEntity();

    $event = $registration->getEvent();
    $form['#title'] = $this->t(
      $registration->isNew() ? 'Create Registration' : 'Edit Registration',
      array('%event_label' => $event->label(), '%event_id' => $event->id(), '%registration_id' => $registration ->id())
    );

    $form = parent::form($form, $form_state, $registration);

    return $form;
  }

  public function save(array $form, FormStateInterface $form_state) {
    $registration = $this->getEntity();
    $is_new = $registration->isNew();
    $registration->save();

    $t_args = array('@type' => $registration->bundle(), '%label' => $registration->label(), '%id' => $registration->id());

    if ($is_new) {
      drupal_set_message(t('Registration has been created.', $t_args));
    }
    else {
      drupal_set_message(t('Registration was updated.', $t_args));
    }

    if ($registration->id()) {
      if ($registration->access('view')) {
        $form_state->setRedirect(
          'entity.registration.canonical',
          array('registration' => $registration->id())
        );
      }
      else {
        $form_state->setRedirect('<front>');
      }
    }
  }
}