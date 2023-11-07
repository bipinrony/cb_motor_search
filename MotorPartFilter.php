<?php

declare(strict_types=1);

namespace Plugin\cb_motor_search;

use JTL\DB\ReturnType;
use JTL\Filter\AbstractFilter;
use JTL\Filter\FilterInterface;
use JTL\Filter\Join;
use JTL\Filter\Option;
use JTL\Filter\ProductFilter;
use JTL\Filter\StateSQL;
use JTL\Filter\Type;

/**
 * Class MotorPartFilter
 * @package Plugin\cb_motor_search
 */
class MotorPartFilter extends AbstractFilter
{
    /**
     * MotorPartFilter constructor
     *
     * @param ProductFilter $productFilter
     */
    public function __construct(ProductFilter $productFilter)
    {
        parent::__construct($productFilter);
        $this->setType(Type::AND)
            ->setUrlParam('cmf')
            ->setName('Motorcycle Part Filter')
            ->setFrontendName($this->getName());
    }

    /**
     * @inheritdoc
     */
    public function setSeo(array $languages): FilterInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyRow(): string
    {
        return 'id';
    }

    /**
     * @inheritdoc
     */
    public function getTableName(): string
    {
        return 'cb_motor_product_mapping';
    }

    /**
     * @inheritdoc
     */
    public function getSQLCondition(): string
    {
        return ' ' . $this->getTableName() . '.part_id = ' . $this->getValue();
    }

    /**
     * @return array|int|string
     */
    public function getValue()
    {
        return (int)parent::getValue();
    }

    /**
     * @inheritdoc
     */
    public function getSQLJoin()
    {
        return (new Join())
            ->setComment('join from Motorcycle Part Filter')
            ->setType('JOIN')
            ->setTable($this->getTableName())
            ->setOn('tartikel.kArtikel = ' . $this->getTableName() . '.kArtikel')
            ->setOrigin(__CLASS__);
    }

    /**
     * @inheritdoc
     */
    public function generateActiveFilterData(): FilterInterface
    {
        parent::generateActiveFilterData();
        // every active value would just be named "Motorcycle Part Filter" - so we just add ': <value>' to it
        foreach ($this->activeValues as $value) {
            $value->setFrontendName($value->getFrontendName() . ': ' . $value->getValue());
        }

        return $this;
    }

    /**
     * @param null $mixed
     * @return array
     */
    public function getOptions($mixed = null): array
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $this->options = [];
        foreach ($this->getOptionData() as $option) {
            $this->options[] = (new Option())
                ->setType($this->getType())
                ->setClassName($this->getClassName())
                ->setParam($this->getUrlParam())
                ->setName($option->name)
                ->setValue((int)$option->part_id)
                ->setCount((int)$option->nAnzahl)
                ->setURL(
                    $this->productFilter->getFilterURL()->getURL(
                        (new self($this->productFilter))->init((int)$option->part_id)
                    )
                );
        }

        return $this->options;
    }

    /**
     * @return array
     */
    private function getOptionData(): array
    {
        $sql = (new StateSQL())->from($this->productFilter->getCurrentStateData());
        $sql->addJoin($this->getSQLJoin());
        $sql->setSelect([$this->getTableName() . '.part_id', $this->getTableName() . '.name', 'tartikel.kArtikel']);
        $sql->setOrderBy('');

        return $this->productFilter->getDB()->query(
            'SELECT ssMerkmal.part_id, ssMerkmal.name, COUNT(*) AS nAnzahl
                FROM (' . $this->productFilter->getFilterSQL()->getBaseQuery($sql) . ' ) AS ssMerkmal
                GROUP BY ssMerkmal.part_id
                ORDER BY ssMerkmal.part_id ASC',
            ReturnType::ARRAY_OF_OBJECTS
        );
    }
}
