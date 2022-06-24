<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Api;

use \Blueskytechco\StoreLocator\Api\Data\StoreInterface;


interface StoreRepositoryInterface
{

    public function get($id);
    public function save(StoreInterface $model);
    public function delete(StoreInterface $model);
    public function deleteById($id);
}
