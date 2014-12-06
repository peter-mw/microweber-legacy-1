<?php
 
$controller = new mvc_example_blade\Controller();

if(isset($params['view']) and method_exists($controller,$params['view'])){
    $view = $params['view'];
    return $controller->$view($params);
} else {
    return $controller->index($params);
}

