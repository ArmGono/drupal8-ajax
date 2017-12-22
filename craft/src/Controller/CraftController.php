<?php

/**
 * @file
 * Содержит \Drupal\cusom_ajax\Controller\CustomAjaxController.
 */

namespace Drupal\cusom_ajax\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;

/**
 * Определяем контролер CustomAjaxController.
 */
class CustomAjaxController extends ControllerBase {

  public function ajax_show_message() {
    // Создаем экземпляр класса AjaxResponse().
    $response = new AjaxResponse();

    // Добавляем команду js: alert().
    $response->addCommand(new AlertCommand('Hello world'));

    //Возвращаем набор команд для выполнения.
    return $response;
  }

}
