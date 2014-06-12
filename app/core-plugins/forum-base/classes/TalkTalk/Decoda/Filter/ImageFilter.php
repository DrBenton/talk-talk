<?php
/**
 * Since I have not been able to customize a Filter tags data after its instantiation,
 * I had to create this sub-class of Decoda\Filter\ImageFilter.
 *
 * The only modification is the addition of ".php" to the constant IMAGE_PATTERN.
 *
 * @copyright   2006-2014, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/decoda/blob/master/license.md
 * @link        http://milesj.me/code/php/decoda
 */

namespace TalkTalk\Decoda\Filter;

use Decoda\Decoda;
use Decoda\Filter\ImageFilter as BaseImageFilter;

/**
 * Provides tags for images.
 */
class ImageFilter extends BaseImageFilter
{
    /**
     * Regex pattern.
     */
    const IMAGE_PATTERN = '/^((?:https?:\/)?(?:\.){0,2}\/)((?:.*?)\.(jpg|jpeg|png|gif|bmp|php))(\?[^#]+)?(#[\-\w]+)?$/is';

    public function construct()
    {
        $this->_tags['img']['contentPattern'] = self::IMAGE_PATTERN;
        $this->_tags['image']['contentPattern'] = self::IMAGE_PATTERN;

        parent::construct();
    }

}
