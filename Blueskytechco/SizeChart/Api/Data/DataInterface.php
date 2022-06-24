<?php
/**
 * Copyright © 2021 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\SizeChart\Api\Data;

interface DataInterface
{
    const NAME = 'name';


    public function getId();
    public function getName();
	
    public function setId($id);
    public function setName($name);
}
