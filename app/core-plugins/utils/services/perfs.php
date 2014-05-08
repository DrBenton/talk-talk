<?php

$app['perfs.script.duration'] = function () use ($app) {
    return round(microtime(true) - $app['perfs.start-time'], 3);
};

$app['perfs.script.nb-included-files'] = function () use ($app) {
    return count(get_included_files());
};
