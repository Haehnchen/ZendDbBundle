<?php

namespace espend\ZendDbBundle\Zend\Db;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\SqlInterface;
use Zend\Db\Sql\Sql as ZendSql;

class ZendDbConnection {

	/**
	 * @var \Zend\Db\Adapter\Adapter
	 */
	var $adapter;

	/**
	 * @var Sql\Sql
	 */
	protected $sql;

	function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}

	function setSql(ZendSql $sql) {
		$this->sql = $sql;
	}

	function getQueryBuilder() {
		return $this->sql;
	}

	function getQueryMode($mode = 'EXECUTE') {
		$adapter = $this->adapter;
		return $mode == 'EXECUTE' ? $adapter::QUERY_MODE_EXECUTE : $adapter::QUERY_MODE_PREPARE;
	}

	function fetchArray(SqlInterface $sql) {
		$selectString = $this->sql->getSqlStringForSqlObject($sql);
		$results = $this->adapter->query($selectString, $this->getQueryMode());
		return $results->toArray();
	}

	function fetchColumn(SqlInterface $sql, $col = 0) {
		$array = $this->fetchArray($sql);
		return isset($array[$col])  ? $array[$col] : null;
	}

	function fetchField(SqlInterface $sql) {
		$array = $this->fetchArray($sql);
		return count($array) > 0 ? current($array[0]) : null;
	}

	function getResult(SqlInterface $sql) {
		return $this->adapter->query($this->sql->getSqlStringForSqlObject($sql), $this->getQueryMode());
	}

	/**
	 * @param string|SqlInterface $select
	 * @return \Zend\Db\Adapter\Driver\StatementInterface|\Zend\Db\ResultSet\ResultSet
	 */
	function execute($select) {

		if($select instanceof SqlInterface) {
			$select = $this->sql->getSqlStringForSqlObject($select);
		}

		return $this->adapter->query($select, $this->getQueryMode());
	}

	/**
	 * @return \Zend\Db\Adapter\Adapter
	 */
	function getAdapter() {
		return $this->adapter;
	}

}