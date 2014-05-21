<?php

use Decoda\Decoda;

$app['forum-base.markup-manager.handle_forum_markup.bbcode'] = $app->protect(
    function ($forumContent) use ($app) {
        $bbDecoder = new Decoda($forumContent, array(
            'xhtmlOutput' => false,
            'strictMode' => false,
            'escapeHtml' => true
        ));
        $bbDecoder->defaults();
        return $bbDecoder->parse();
    }  
);

$app['forum-base.markup-manager.handle_forum_markup.smilies'] = $app->protect(
    function ($forumContent) use ($app) {
        $forumContent = preg_replace(
            '~<!-- s[^ >]+ --><img src="([^"]+)" alt="([^"]+)"[^>]+/?><!-- s[^ >]+ -->~i',
            '[img alt="$2"]$1[/img]',
            $forumContent
        );
        $forumContent = str_replace(
            '{SMILIES_PATH}',
            'http://www.japafigs.com/images/smilies/',//our guinea pig forum URL :-)
            $forumContent
        );
        
        return $forumContent;
    }  
);

$app['forum-base.markup-manager.handle_forum_markup.links'] = $app->protect(
    function ($forumContent) use ($app) {
        $forumContent = preg_replace(
            '~<!-- [ml] --><a\s+[^>]*href="([^"]+)"[^>]*>([^<>]+)</a><!-- [ml] -->~i',
            '[url="$1"]$2[/url]',
            $forumContent
        );
        
        return $forumContent;
    }  
);

$app['forum-base.markup-manager.handle_forum_markup.add_blank_targets'] = $app->protect(
    function ($forumContent) use ($app) {
        $forumContent = preg_replace(
            '~<a\s+([^>]*href="[^"]+"[^>]*)>~i',
            '<a $1 target="_blank">',
            $forumContent
        );

        return $forumContent;
    }
);

$app['forum-base.markup-manager.handle_forum_markup.all'] = $app->protect(
    function ($forumContent) use ($app) {
        $forumContent = $app['forum-base.markup-manager.handle_forum_markup.smilies']($forumContent);
        $forumContent = $app['forum-base.markup-manager.handle_forum_markup.links']($forumContent);
        $forumContent = $app['forum-base.markup-manager.handle_forum_markup.bbcode']($forumContent);
        $forumContent = $app['forum-base.markup-manager.handle_forum_markup.add_blank_targets']($forumContent);

        return $forumContent;
    }  
);