<?php
/**
 * Copyright © 2021 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\SizeChart\Model;

use \Blueskytechco\SizeChart\Api\RepositoryInterface;
use \Blueskytechco\SizeChart\Model\ResourceModel\SizeChart as ResourceModel;
use \Blueskytechco\SizeChart\Api\Data\DataInterface;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\StateException;

class SizeChartRepository implements RepositoryInterface
{

    private $resourceModel;
    private $modelFactory;
    private $instances = [];


    public function __construct(
        ResourceModel $resourceModel,
        SizeChartFactory $modelFactory
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
                throw NoSuchEntityException::singleField('entity_id', $id);
            }

            $this->instances[$id] = $model;
        }

        return $this->instances[$id];
    }

    public function save(DataInterface $model)
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

    public function delete(DataInterface $model)
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
