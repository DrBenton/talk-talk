<?php

$action = function () {
    ob_start();
    phpinfo();

    return ob_get_flush();
};

return $action;
