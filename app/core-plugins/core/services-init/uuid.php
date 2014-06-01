<?php

$app['uuid'] = function () {
    //TODO: use a stronger "unique id" function
    //(which generates shorted ids than UUID, if possible - a la MongoDB?)
    return uniqid();
};
