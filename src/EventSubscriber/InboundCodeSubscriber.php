<?php

namespace Drupal\visitor_tracking_inbound_code\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InboundCodeSubscriber implements EventSubscriberInterface {

  public function checkForRedirection(GetResponseEvent $event) {
    $request_keys = visitor_tracking_inbound_code_get_request_keys();

    $set = [];

    foreach ($request_keys as $key => $label) {
      if ($event->getRequest()->request->has($key)) {
        $set[$key] = $event->getRequest()->request->get($key);
      }
    }

    if (!empty($set)) {
      /** @var \Drupal\Core\Session\AccountProxyInterface $current_user */
      $current_user = \Drupal::service('current_user');

      if ($current_user->isAnonymous()) {
        $_SESSION['forced'] = TRUE;
        $event->getRequest()->getSession()->start();
      }

      /** @var \Drupal\user\PrivateTempStoreFactory $private_tempstore_factory */
      $private_tempstore_factory = \Drupal::service('user.private_tempstore');
      $tempstore = $private_tempstore_factory->get('visitor_tracking_inbound_code');

      $codes = $tempstore->get('tracking_codes');
      if (empty($codes)) {
        $codes = [];
      }
      foreach ($set as $key => $code) {
        $codes[$key] = $code;
      }

      $tempstore->set('tracking_codes', $codes);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::REQUEST][] = array('checkForRedirection');
    return $events;
  }

}
