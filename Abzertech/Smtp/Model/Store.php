<?php

namespace Abzertech\Smtp\Model;

class Store
{

    /**
     @var int|null
     */
    protected $store_id = null;

    /**
     * @var null
     */
    protected $from = null;

    /**
     * Render StoreId
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->store_id;
    }

    /**
     * Set StoreId
     *
     * @param $store_id
     * @return $this
     */
    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
        return $this;
    }

    /**
     * return from
     *
     * @return string|array
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * set from
     *
     * @param string|array $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }
}
