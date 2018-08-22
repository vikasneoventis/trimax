<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model;

use Aitoc\ProductUnitsAndQuantities\Api\Data;
use Aitoc\ProductUnitsAndQuantities\Api\GeneralRepositoryInterface;
use Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface as GeneralModel;
use Aitoc\ProductUnitsAndQuantities\Model\ResourceModel\General as GeneralResourceModel;
use Aitoc\ProductUnitsAndQuantities\Model\GeneralFactory as GeneralModelFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class GeneralRepository implements GeneralRepositoryInterface
{
    /** @var array */
    private $entities;

    /** @var GeneralModel */
    private $generalModel;

    /** @var GeneralResourceModel */
    private $generalResourceModel;

    /** @var GeneralModelFactory */
    private $generalModelFactory;

    public function __construct(
        GeneralModel $generalModel,
        GeneralResourceModel $generalResourceModel,
        GeneralModelFactory $generalModelFactory
    ) {
        $this->entities = [];
        $this->generalModel = $generalModel;
        $this->generalResourceModel = $generalResourceModel;
        $this->generalModelFactory = $generalModelFactory;
    }

    /**
     * @param GeneralModel $generalModel
     * @return GeneralModel
     * @throws CouldNotSaveException
     */
    public function save(Data\GeneralInterface $generalModel)
    {
        if ($generalModel->getItemId()) {
            $model = $this->get($generalModel->getItemId())->addData($generalModel->getData());
        }

        try {
            $this->generalResourceModel->save($model);
            if ($model->getItemId()) {
                unset($this->entities[$model->getItemId()]);
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save settings %1', $model->getItemId()));
        }
        return $model;

    }

    /**
     * @param int $entityId
     * @return GeneralModel|mixed
     * @throws NoSuchEntityException
     */
    public function getById($entityId)
    {
        if (!isset($this->entities[$entityId])) {
            $model = $this->generalModelFactory->create();
            $this->generalResourceModel->load($model, $entityId);
            if (!$model->getItemId()) {
                throw new NoSuchEntityException(__('Settings with specified ID "%1" not found.', $entityId));
            }
            $this->entities[$entityId] = $model;
        }
        return $this->entities[$entityId];
    }

    /**
     * @param GeneralModel $generalModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\GeneralInterface $generalModel)
    {
        try {
            $this->generalResourceModel->delete($generalModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;

    }

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->getById($entityId));
    }
}
