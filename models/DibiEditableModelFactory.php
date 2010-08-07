<?php
namespace Gridito;

use \Nette\Object;
use \dibi;

/**
 * @author Vít Šesták
 * @copyright Vít Šesták
 * An factory/builder for DibiEditableModel.
 */

final class DibiEditableModelFactory extends Object{
	
	private $connection;
	
	private $table;
	
	private $updateHandler = array(__CLASS__, '_bracket');
	
	private $createHandler = array(__CLASS__, '_bracket');
	
	private $filter=array();
	
	private $primaryKey='id';
	
	/**
	 * @access private
	 */
	function __construct($table){
		$this->table = $table;
	}
	
	static function _bracket($v){
		return $v;
	}

	///////////////////

	public function setTable($v){
		$this->table=$v;
		return $this;
	}

	public function setConnection($v){
		$this->connection=$v;
		return $this;
	}

	public function setUpdateHandler($v){
		$this->updateHandler=$v;
		return $this;
	}

	public function setCreateHandler($v){
		$this->createHandler=$v;
		return $this;
	}

	public function setFilter($v){
		$this->filter=$v;
		return $this;
	}

	public function setPrimaryKey($v){
		$this->primaryKey=$v;
		return $this;
	}

	public function build(){
		return new DibiEditableModel($this->connection ?: dibi::getConnection(), $this->table, $this->primaryKey, $this->filter, $this->updateHandler, $this->createHandler);
	}

};