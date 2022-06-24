<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\StoreLocator\Model;

use \Blueskytechco\StoreLocator\Api\StoreRepositoryInterface;
use \Blueskytechco\StoreLocator\Model\ResourceModel\Store as ResourceModel;
use \Blueskytechco\StoreLocator\Api\Data\StoreInterface;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\StateException;

class StoreRepository implements StoreRepositoryInterface
{

    private $resourceModel;
    private $modelFactory;
    private $instances = [];


    public function __construct(
        ResourceModel $resourceModel,
        StoreFactory $modelFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->modelFactory = $modelFactory;
    }

    public function get($id)
    {
        if (!isset($this->instances[$id])) {
            $model = $this->modelFactory->create();

            $model->load($id);

            if (!$model->getId()) {
                throw NoSuchEntityException::singleField('store_id', $id);
            }

            $this->instances[$id] = $model;
        }

        return $this->instances[$id];
    }

    public function save(StoreInterface $model)
    {
        try {
            $existingModel = $this->get($model->getId());
        } catch (NoSuchEntityException $e) {
            $existingModel = null;
        }

        if ($existingModel !== null) {
            foreach ($existingModel->getData() as $key => $value) {
                if (!$model->hasData($key)) {
                    $model->setData($key, $value);
                }
            }
        }

        $this->resourceModel->save($model);
        unset($this->instances[$model->getId()]);

        return $this->get($model->getId());
    }

    public function delete(StoreInterface $model)
    {
        $name = $model->getName();
        try {
            unset($this->instances[$model->getId()]);
            $this->resourceModel->delete($model);
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove store %1', $name)
            );
        }
        unset($this->instances[$model->getId()]);

        return true;
    }

    public function deleteById($id)
    {
        $model = $this->get($id);

        return $this->delete($model);
    }
}
