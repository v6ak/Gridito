<?php

namespace Gridito;

use Ormion\Collection;
use Nette\Object;

/**
 * Ormion model
 *
 * @author Jan Marek
 * @license MIT
 */
class OrmionModel extends Object implements IModel {

	// <editor-fold defaultstate="collapsed" desc="variables">

	/** @var Collection */
	private $collection;

	/** @var string */
	private $rowClass;

	// </editor-fold>

	/**
	 * Constructor
	 * @param Collection $collection data
	 */
	public function __construct(Collection $collection) {
		$this->collection = $collection;
		$this->rowClass = $collection->getItemType();
	}


	/**
	 * Get iterator
	 */
	public function getIterator() {
		return $this->collection->getIterator();
	}


	/**
	 * Process action parameter
	 * @param mixed $param
	 * @return mixed
	 */
	public function processActionParam($param) {
		if ($param === null) {
			return null;
		}

		$class = $this->rowClass;
		return $class::create($param);
	}


	/**
	 * Setup grid after model connect
	 * @param Grid $grid
	 */
	public function setupGrid(Grid $grid) {
		$class = $this->rowClass;
		$grid->setPrimaryKey($class::getConfig()->getPrimaryColumn());
	}


	/**
	 * Set sorting
	 * @param string $column
	 * @param string $type asc or desc
	 */
	public function setSorting($column, $type) {
		$this->collection->removeClause("orderBy")->orderBy("[$column] $type");
	}


	/**
	 * Item count
	 * @return int
	 */
	public function count() {
		return $this->collection->count();
	}


	/**
	 * Set limit
	 * @param int $offset
	 * @param int $limit
	 */
	public function setLimit($offset, $limit) {
		$this->collection->removeClause("offset")->offset($offset);
		$this->collection->removeClause("limit")->limit($limit);
	}

}