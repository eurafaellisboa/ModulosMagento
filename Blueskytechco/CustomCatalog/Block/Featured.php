<?php
namespace Blueskytechco\CustomCatalog\Block;

class Featured extends \Magefan\Blog\Block\Sidebar\Featured
{
    public function getShorContent($post)
    {
        return $post->getShortFilteredContent();
    }
}
