<?php
/**
 * Copyright © 2021 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\SizeChart\Api;

use \Blueskytechco\SizeChart\Api\Data\DataInterface;


interface RepositoryInterface
{

    public function get($id);
    public function save(DataInterface $model);
    public function delete(DataInterface $model);
    public function deleteById($id);
}
