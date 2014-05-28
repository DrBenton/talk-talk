<?php

use TalkTalk\Core\Utils\ArrayUtils;
use QueryPath\DOMQuery;

$app['html-components.add_component'] = $app->protect(
  function (DOMQuery $node, $componentsNames) {
      $nodeCurrentComponents = explode(',', $node->attr('data-component'));
      $componentsNames = ArrayUtils::getArray($componentsNames);
      $nodeCurrentComponents = array_merge($nodeCurrentComponents, $componentsNames);
      $nodeCurrentComponents = array_filter($nodeCurrentComponents, function ($componentName) {
          return is_string($componentName) && strlen($componentName) > 0;
      });
      $node->addClass('flight-component');
      $node->attr('data-component', implode(',', $nodeCurrentComponents));
  }
);
