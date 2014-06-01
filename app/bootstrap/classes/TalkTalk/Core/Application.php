<?php

namespace TalkTalk\Core;

use Slim\Slim;

class Application extends Slim {

    /**
     * @var array
     */
    public $vars = array();

    /**
     * Includes a file within an isolated Closure, and returns its result (if any).
     * The file PHP core will only have access to a "$app" variable.
     *
     * @param  string $filePath
     * @throws \RuntimeException
     * @return mixed
     */
    public function includeFileInIsolatedClosure($filePath)
    {
        $app = &$this;

        // Let's append the ".php" file extension if not already present
        $filePath .= (preg_match('~\.php$~i', $filePath)) ? '' : '.php' ;

        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf('File path "%s" not found!', $filePath));
        }

        // A small security check: we only allow files inside the app directory
        $fileRealPath = realpath($filePath);
        if (0 !== strpos($fileRealPath, $app->vars['app.path'])) {
            throw new \RuntimeException(sprintf('File path "%s" is not inside app directory!', $filePath));
        }

        $__includedFilePath = $fileRealPath;

        return call_user_func(
            function () use (&$app, $__includedFilePath) {
                return include_once $__includedFilePath;
            }
        );
    }

}