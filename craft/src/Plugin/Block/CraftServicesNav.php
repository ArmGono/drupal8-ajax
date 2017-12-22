<?php

/**
 * @file
 * Contains \Drupal\craft\Plugin\Block\CraftServicesNav.
 */

namespace Drupal\craft\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;
use Drupal\Component\Render\FormattableMarkup;

/**
 * @Block(
 *   id = "craft_service_block",
 *   admin_label = @Translation("Show navigation on service"),
 * )
 */
class CraftServicesNav extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $block = [];
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node && $node->getType() == 'services') {
      foreach ($node->field_info as $info) {
        if ($info->entity) {
          $id = $info->entity->id();
          $title = str_replace('|', '<br>', $info->entity->label());
          $svg = explode('|', $info->entity->field_svg->value);
          $svg_name = array_shift($svg);

          if (!empty($svg)) {
            list($width, $heigth) = explode(',', $svg[0]);
          }
          else {
            $width = 50;
            $heigth = 50;
          }
          $svgAttributes = new Attribute([
            'class' => ['use-svg'],
            'data-svg-name' => $svg_name,
            'data-svg-width' => $width,
            'data-svg-height' => $heigth,
          ]);
          $block['items'][$id] = $this->getInfoItem($title, $id, $svgAttributes);
        }
      }
      if (!empty($block)) {
        $block['items']['#prefix'] = '<div id="services-items" class="clearfix">';
        $block['items']['#suffix'] = '</div>';
        $label = $node->field_infonames->value;
        $block['label'] = [
          '#weight' => -10,
          '#prefix' => '<div class="services-nav-label">',
          '#suffix' => '</div>',
          '#markup' => $label,
        ];
      }
    }
    return $block;
  }

  private function getInfoItem($title, $id, Attribute $attributes) {
    $url = '/ajax/craft/cards/' . $id;
    return [
      '#prefix' => '<div class="service-item">',
      '#suffix' => '</div>',
      'svg' => [
        '#markup' => '<div' . $attributes . '></div>'
      ],
      'title' => [
        '#markup' => '<a href="' . $url . '" class="use-ajax">' . $title . '</a>',
        '#prefix' => '<div class="service-nav-title">',
        '#suffix' => '</div>',
      ],
    ];
  }

}
