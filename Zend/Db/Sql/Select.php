<?php

namespace espend\ZendDbBundle\Zend\Db\Sql;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Db\Sql\Select as ZendSelect;
use Zend\Db\Exception\InvalidArgumentException;
use espend\ZendDbBundle\Exception\MissingObjectManagerException;

class Select extends ZendSelect {

	/**
	 * @var ObjectManager
	 */
	protected $om = null;

	public function __construct($table = null, ObjectManager $om = null) {

		if ($om) {
			$this->setObjectManager($om);
		}

		parent::__construct($table);
	}

	/**
	 * Create from clause
	 *
	 * @param  string|array|\Zend\Db\Sql\TableIdentifier $table
	 * @throws \Zend\Db\Sql\Exception\InvalidArgumentException
	 * @return Select
	 */
	public function from($table) {
		return parent::from($this->getDoctrineTableNameWithAlias($table));
	}

	/**
	 * Create join clause
	 *
	 * @param  string|array $name
	 * @param  string $on
	 * @param  string|array $columns
	 * @param  string $type one of the JOIN_* constants
	 * @throws InvalidArgumentException
	 * @return Select
	 */
	public function join($name, $on, $columns = self::SQL_STAR, $type = self::JOIN_INNER) {

		if(is_string($name)) {
			$name = $this->getDoctrineTableNameWithAlias($name);
		} elseif (is_array($name)) {
			$name[key($name)] = $this->getDoctrineTableName($name[key($name)]);
		}

		parent::join($name, $on, $columns, $type);
	}

	protected function getDoctrineTableNameWithAlias($table) {

		if (is_string($table)) {

			// match entity class or shortcut names  and generate tableize aliases
			if (($match = strrpos($table, '\\')) !== false OR ($match = strrpos($table, ':')) !== false) {

				// check entity name match alias name, so we dont need any alias
				if (Sql::tableize(substr($table, $match + 1)) != $this->getDoctrineTableName($table)) {
					$table = array(Sql::tableize(substr($table, $match + 1)) => $this->getDoctrineTableName($table));
				} else {
					$table = $this->getDoctrineTableName($table);
				}
			}

		} elseif (is_array($table) && (is_string(key($table)) || count($table) === 1)) {

			// to support the zend/db alias: remapped the entity name on the array value to table
			if (($match = strrpos(key($table), '\\')) !== false OR ($match = strrpos(key($table), ':')) !== false) {
				$table = array($this->getDoctrineTableName($table[key($table)]) => Sql::tableize(substr(key($table), $match + 1)));
			}

		}

		return $table;
	}

	protected function getDoctrineTableName($table) {

		if (strpos($table, '\\') === false AND strpos($table, ':') === false) {
			return $table;
		}

		if ($this->om === null) {
			throw new MissingObjectManagerException();
		}

		return $this->om->getClassMetadata($table)->getTableName();
	}

	function setObjectManager(ObjectManager $om) {
		$this->om = $om;
	}

}