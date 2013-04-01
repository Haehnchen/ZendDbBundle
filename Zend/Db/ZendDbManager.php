<?php

namespace espend\ZendDbBundle\Zend\Db;

use Zend\Db\Adapter\Adapter;
use Doctrine\Common\Persistence\ObjectManager;
use espend\ZendDbBundle\Zend\Db\Sql\Sql;

class ZendDbManager {

	/**
	 * @var Adapter
	 */
	protected $adapter = array();

	protected $connections = array();

	/**
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	protected $om;

	function __construct(ObjectManager $om) {
		$this->om = $om;
	}

	function addAdapter($name, Adapter $adapter) {
		$this->adapter[$name] = $adapter;
	}

	/**
	 * @param string $name
	 * @return ZendDbConnection
	 * @throws \RuntimeException
	 */
	function getManager($name = 'default') {

		if(!isset($this->adapter[$name])) {
			throw new \RuntimeException('invalid zend db connection');
		}

		if(!isset($this->connections[$name])) {
			$this->connections[$name] = new ZendDbConnection($this->adapter[$name]);
			$zend_sql = new Sql($this->adapter[$name]);
			$zend_sql->setObjectManager($this->om);

			$this->connections[$name]->setSql($zend_sql);
		}

		return $this->connections[$name];
	}


}