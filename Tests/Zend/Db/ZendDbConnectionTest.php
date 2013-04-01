<?php

namespace espend\ZendDbBundle\Tests\Zend\Db;

use Zend\Db\Adapter\Adapter;
use espend\ZendDbBundle\Zend\Db\ZendDbConnection;

class ZendDbConnectionTest extends \PHPUnit_Framework_TestCase {


	private $zend_sql;
	private $sql;
	private $adapter;
	private $result;
	private $sql_return;

	function setUp() {

		$this->sql_return = array(array('id' => 1, 'name' => 'john'));

		$this->zend_sql = $this->getMockBuilder('Zend\Db\Sql\Sql')->disableOriginalConstructor()->getMock();
		$this->zend_sql->expects($this->once())->method('getSqlStringForSqlObject');

		$this->sql = $this->getMockBuilder('Zend\Db\Sql\SqlInterface')->disableOriginalConstructor()->getMock();

		$this->result = $this->getMockBuilder('Zend\Db\ResultSet\ResultSet')->disableOriginalConstructor()->getMock();
		$this->result->expects($this->once())->method('toArray')->will($this->returnValue($this->sql_return));

		$this->adapter = $this->getMockBuilder('Zend\Db\Adapter\Adapter')->disableOriginalConstructor()->getMock();
		$this->adapter->expects($this->once())->method('query')->will($this->returnValue($this->result));

	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbConnection::fetchArray
	 */
	function testFetchArray() {
		$conn = new ZendDbConnection($this->adapter);
		$conn->setSql($this->zend_sql);
		$this->assertEquals($this->sql_return, $conn->fetchArray($this->sql));

	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbConnection::fetchField
	 */
	function testFetchField() {
		$conn = new ZendDbConnection($this->adapter);
		$conn->setSql($this->zend_sql);
		$this->assertEquals(1, $conn->fetchField($this->sql));
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbConnection::fetchColumn
	 */
	function testFetchColumn() {
		$conn = new ZendDbConnection($this->adapter);
		$conn->setSql($this->zend_sql);
		$this->assertEquals(current($this->sql_return), $conn->fetchColumn($this->sql));
	}

}