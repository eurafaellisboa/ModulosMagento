<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-03-02 09:13:16
 * @@Modify Date: 2018-03-02 19:25:09
 * @@Function:
 */

namespace Magiccart\Testimonial\Block\Post\PostList;

/**
 * Testimonial list item
 */
class Item extends \Magiccart\Testimonial\Block\Post\AbstractPost
{

     /**
     * Retrieve post content
     *
     * @return url
     */
    public function getContent()
    {
        return $this->getPost()->getFilteredContent();
    }

}
