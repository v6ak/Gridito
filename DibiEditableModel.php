<?php
namespace Gridito;

use \DibiConnection,
	\DibiFluent;

/**
 * @author Vít Šesták
 * @copyright Vít Šesták
 * An IEditableModel based on dibi database layer.
 */

final class DibiEditableModel extends DibiModel implements IEditableModel{

	/** @var DibiConnection */
	private $dbh;

	/** @var string */
	private $table;

	/** @var array */
	private $filter;

	private $updateHandler;
	
	private $createHandler;

	/**
	 * @access private
	 */
	public function __construct(DibiConnection $dbh, $table, $primaryKey='id', $filter=array(), $updateHandler=array(__CLASS__, '_bracket'), $createHandler=array(__CLASS__, '_bracket')){
		$this->dbh = $dbh;
		$this->table = $table;
		$this->filter = $filter;
		$this->primaryKey = $primaryKey;
		$this->updateHandler = $updateHandler;
		$this->createHandler = $createHandler;
	}
	
	static function _bracket($v){
		return $v;
	}

	private function createSelect($cols='*'){
		return $this->dbh->select($cols)->from($this->table);
	}

	private function addFilter(DibiFluent $q, $where){
		foreach($this->filter as $col=>$value) {
			if($where){
				$q->and("[$col] = %s", $value);
			}else{
				$q->where("[$col] = %s", $value);
				$where = true;
			}
		}
	}

	private function addWhereFor(DibiFluent $q, $id){
		$q->where("[$this->primaryKey] = %s", $id);
		$this->addFilter($q, true);
	}
	
	private function createSelectFor($id, $cols='*'){
		$q = $this->createSelect($cols);
		$this->addWhereFor($q, $id);
		return $q;
	}

	protected function getSource(){
		$q=$this->createSelect();
		$this->addFilter($q, false);
		return $q;
	}

	public function offsetGet($id){
		return $this->createSelectFor($id)->fetch();
	}
	
	public function offsetExists($id){
		return $this->createSelectFor($id, 'COUNT(*)')->fetchSingle() == 1;
	}
	
	public function offsetUnset($id){
		$q = $this->dbh->delete($this->table);
		$this->addWhereFor($q, $id);
		$q->execute();
		return $this->dbh->affectedRows() == 1;
	}

	public function offsetSet($id, $rawValues){
		// TODO: consider checking $vals against $this->filter
		$handler = ($id === null) ?$this->createHandler :$this->updateHandler;
		$vals = call_user_func($handler, $rawValues) + $this->filter; // TODO: consider order etc.
		if($id === null){
			$this->dbh->insert($this->table, $vals)->execute();
			return $this->dbh->getInsertId();
		}else{
			$q = $this->dbh->update($this->table, $vals);
			$this->addWhereFor($q, $id);
			$q->execute();
			return $this->dbh->affectedRows();
		}
	}
	
	public static function factory($table=null){
		return new DibiEditableModelFactory($table);
	}
	
}