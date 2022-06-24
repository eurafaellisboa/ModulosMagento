<?php

namespace MGS\Blog\Model\Resource\Category;

use MGS\Blog\Model\Resource\CategoryCollection;

class Collection extends CategoryCollection
{
    protected $_idFieldName = 'category_id';
    protected $_previewFlag;

    protected function _construct()
    {
        $this->_init('MGS\Blog\Model\Category', 'MGS\Blog\Model\Resource\Category');
        $this->_map['fields']['category_id'] = 'main_table.category_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    public function toOptionIdArray()
    {
        $res = [];
        $existingIdentifiers = [];
        foreach ($this as $item) {
            $identifier = $item->getData('url_key');
            $data['value'] = $identifier;
            $data['label'] = $item->getData('title');
            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('category_id');
            } else {
                $existingIdentifiers[] = $identifier;
            }
            $res[] = $data;
        }
        return $res;
    }

    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    public function addPostFilter($postId)
    {
        $this->getSelect()
            ->join(
                ['category_table' => $this->getTable('mgs_blog_category_post')],
                'main_table.category_id = category_table.category_id',
                []
            )
            ->where('category_table.post_id = ?', $postId);
        return $this;
    }

    protected function _afterLoad()
    {
        $this->performAfterLoad('mgs_blog_category_store', 'category_id');
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('mgs_blog_category_store', 'category_id');
    }
}
