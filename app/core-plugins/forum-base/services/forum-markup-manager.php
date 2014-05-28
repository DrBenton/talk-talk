<?php

use Decoda\Decoda;
use TalkTalk\Decoda\Filter\ImageFilter as CustomImageFilter;

$app['forum-base.markup-manager.handle_forum_markup.bbcode'] = $app->protect(
    function ($forumContent) use ($app) {
        $bbDecoder = new Decoda($forumContent, array(
            'xhtmlOutput' => false,
            'strictMode' => false,
            'escapeHtml' => true
        ));
        $bbDecoder->defaults();

        /*
        $imgFilter = $bbDecoder->getFilterByTag('img');
        $imgFiltersTags = &$imgFilter->getTag('img');
        $imgFiltersTags['contentPattern'] =
            '/^((?:https?:\/)?(?:\.){0,2}\/)((?:.*?)\.(jpg|jpeg|png|gif|bmp|php))(\?[^#]+)?(#[\-\w]+)?$/is';
        */
        $bbDecoder->removeFilter('Image');
        $bbDecoder->addFilter(new CustomImageFilter());

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
            $app['settings']->get('app.smilies.location', 'upload/smilies/'),
            $forumContent
        );

        return $forumContent;
    }
);

$app['forum-base.markup-manager.handle_forum_markup.links'] = $app->protect(
    function ($forumContent) use ($app) {
        $forumContent = preg_replace(
            '~<!-- [a-z] --><a\s+[^>]*href="([^"]+)"[^>]*>([^<>]+)</a><!-- [a-z] -->~i',
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
