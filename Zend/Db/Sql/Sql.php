<?php

namespace espend\ZendDbBundle\Zend\Db\Sql;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Db\Sql\Sql as ZendSql;

class Sql extends ZendSql {

	/**
	 * @var ObjectManager;
	 */
	protected $om;

	/**
	 * @param null $entity_name
	 * @return Select|\Zend\Db\Sql\Select
	 */
	public function select($entity_name = null) {
		return new Select($entity_name, $this->om);
	}

	/**
	 * @param null $entity_name
	 * @return \Zend\Db\Sql\Delete
	 */
	public function delete($entity_name = null) {
		return parent::delete($this->getTableName($entity_name));
	}

	/**
	 * @param null $entity_name
	 * @return \Zend\Db\Sql\Update
	 */
	public function update($entity_name = null) {
		return parent::update($this->getTableName($entity_name));
	}

	/**
	 * @param null $entity_name
	 * @return \Zend\Db\Sql\Insert
	 */
	public function insert($entity_name = null) {
		return parent::insert($this->getTableName($entity_name));
	}

	function setObjectManager(ObjectManager $om) {
		$this->om = $om;
		return $this;
	}

	protected function getTableName($entity_name) {

		if (strpos($entity_name, '\\') === false AND strpos($entity_name, ':') === false) {
			return $entity_name;
		}

		return $this->om->getClassMetadata($entity_name)->getTableName();
	}

	static function tableize($name) {
		return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $name));
	}


}