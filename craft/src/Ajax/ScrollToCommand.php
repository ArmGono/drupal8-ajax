<?php

namespace Drupal\craft\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class ScrollToCommand implements CommandInterface {

  /**
   * A CSS selector string.
   *
   * If the command is a response to a request from an #ajax form element then
   * this value can be NULL.
   *
   * @var string
   */
  protected $selector;

  /**
   * Offset of scrolling
   *
   * @var int
   */
  protected $offset;

  /**
   * Constructs an ScrollToCommand object.
   *
   * @param string $selector
   *   A jQuery selector.
   * @param array $arguments
   *   An optional array of arguments to pass to the method.
   */
  public function __construct($selector, $offset = 0) {
    $this->selector = $selector;
    $this->offset = $offset;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render() {

    return array(
      'command' => 'scrollTo',
      'selector' => $this->selector,
      'offset' => $this->offset,
    );
  }

}
