<?php

namespace TalkTalk\Core\Service;

class IOUtils extends BaseService
{

    /**
     * Recursive glob
     * @author http://www.g33k-zone.org/
     * @LICENSE Unknown
     * @see http://www.g33k-zone.org/post/2010/05/27/Fonction-glob-r%C3%A9cursive
     */
    public function rglob($pattern = '*', $path = '', $flags = 0)
    {
        if (null !== $this->app && false === strpos($path, $this->app->vars['app.root_path'])) {
            $path = $this->app->vars['app.root_path'] . '/' . $path ;
        }

        $paths = glob($path . '*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
        $files = glob($path . $pattern, $flags);
        foreach ($paths as $path) {
            $files = array_merge($files, $this->rglob($pattern, $path, $flags));
        }

        return $files;
    }

}
