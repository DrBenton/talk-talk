<?php

use Decoda\Decoda;
use TalkTalk\Decoda\Filter\ImageFilter as CustomImageFilter;

$app->defineFunction(
    'forum-base.markup-manager.handle_forum_markup.bbcode',
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

//TODO: rename "{SMILIES_PATH}" to "{SMILEYS_PATH}" (and convert it at phpBB import)
$app->defineFunction(
    'forum-base.markup-manager.handle_forum_markup.smileys',
    function ($forumContent) use ($app) {
        $forumContent = preg_replace(
            '~<!-- s[^ >]+ --><img src="([^"]+)" alt="([^"]+)"[^>]*/?><!-- s[^ >]+ -->~i',
            '[img alt="$2"]$1[/img]',
            $forumContent
        );
        $forumContent = str_replace(
            '{SMILIES_PATH}',
            $app->get('settings')->get('app.smileys.location'),
            $forumContent
        );

        return $forumContent;
    }
);

$app->defineFunction(
    'forum-base.markup-manager.handle_forum_markup_before_save.smileys',
    function ($forumContent) use ($app) {

        $smileys = \TalkTalk\Model\Smiley::all(array('code', 'emotion', 'url'));

        foreach ($smileys as $smiley) {
            $forumContent = str_replace(
              $smiley->code,
              '<!-- s'.$smiley->code.' --><img src="{SMILIES_PATH}/'.$smiley->url.'" alt="'.$smiley->emotion.'"><!-- s'.$smiley->code.' -->',
              $forumContent
            );
        }

        return $forumContent;
    }
);

$app->defineFunction(
    'forum-base.markup-manager.handle_forum_markup.links',
    function ($forumContent) use ($app) {
        $forumContent = preg_replace(
            '~<!-- [a-z] --><a\s+[^>]*href="([^"]+)"[^>]*>([^<>]+)</a><!-- [a-z] -->~i',
            '[url="$1"]$2[/url]',
            $forumContent
        );

        return $forumContent;
    }
);

$app->defineFunction(
    'forum-base.markup-manager.handle_forum_markup.add_blank_targets',
    function ($forumContent) use ($app) {
        $forumContent = preg_replace(
            '~<a\s+([^>]*href="[^"]+"[^>]*)>~i',
            '<a $1 target="_blank">',
            $forumContent
        );

        return $forumContent;
    }
);

$app->defineFunction(
    'forum-base.markup-manager.handle_forum_markup.all',
    function ($forumContent) use ($app) {
        $forumContent = $app->exec('forum-base.markup-manager.handle_forum_markup.smileys', $forumContent);
        $forumContent = $app->exec('forum-base.markup-manager.handle_forum_markup.links', $forumContent);
        $forumContent = $app->exec('forum-base.markup-manager.handle_forum_markup.bbcode', $forumContent);
        $forumContent = $app->exec('forum-base.markup-manager.handle_forum_markup.add_blank_targets', $forumContent);

        return $forumContent;
    }
);
