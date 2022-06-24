<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Api\Data;

interface StoreInterface
{
    const NAME = 'name';
    const COUNTRY = 'country';
    const CREATION_TIME = 'created_at';
    const UPDATE_TIME = 'updated_ad';
    const IS_ACTIVE = 'is_active';


    public function getId();
    public function getName();
    public function getCountry();
    public function getCreationTime();
    public function getUpdateTime();
    public function isActive();
	
    public function setId($id);
    public function setName($name);
    public function setCountry($country);
    public function setCreationTime($creationTime);
    public function setUpdateTime($updateTime);
    public function setIsActive($isActive);
}
