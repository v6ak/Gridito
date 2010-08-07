<?php
namespace Gridito;

use \Gridito\IModel;
use \DibiFluent;
use \Nette\Object;
use \StdClass;

/**
 * @author Vít Šesták
 * An IModel based on dibi database layer.
 */

abstract class DibiModel extends Object implements IModel{ // TODO: consider renaming to AbstractDibiModel
	
	/** @var int */
	private $limit, $offset;
	
	/** @var string */
	protected $primaryKey;
	
	/** @var StdClass (object)array('column'=>col, 'type'=>type) or null */
	private $sorting;
	
	public final function setLimit($offset, $limit){
		$this->limit = $limit;
		$this->offset = $offset;
	}
	
	public function setSorting($column, $type){
		$this->sorting = (object)get_defined_vars(); // TODO: verify if this is according the API
	}
	
	public function processActionParam($param){
		return $param;
	}
	
	protected abstract function getSource();
	
	public final function getIterator(){
		$q = $this->getSource()
			->limit($this->limit)
			->offset($this->offset);
		if($this->sorting){
			$q->orderBy($this->sorting->column, $this->sorting->type);
		}
		return $q->getIterator();
	}
	
	public final function count(){
		return $this->getSource()->count();
	}
	
	public final function setupGrid(Grid $grid){
		$grid->setPrimaryKey($this->primaryKey);
	}
	
	public static function create(DibiFluent $st, $primaryKey='id'){
		return new DibiModel_1($st, $primaryKey);
	}
	
}

class DibiModel_1 extends DibiModel{
	
	/** @var DibiFluent */
	private $statement;
	
	public function __construct(DibiFluent $st, $primaryKey){
		$this->statement = $st;
		$this->primaryKey = $primaryKey;
	}
	
	public function getSource(){
		return clone $this->statement;
	}
	
}