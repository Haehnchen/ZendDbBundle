<?php

namespace espend\ZendDbBundle\Tests\Zend\Db;

use Zend\Db\Adapter\Adapter;
use espend\ZendDbBundle\Zend\Db\ZendDbConnection;

class ZendDbConnectionTest extends \PHPUnit_Framework_TestCase {

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	private $zend_sql;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	private $sql;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	private $result;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	private $adapter;

	private $sql_return;

	function setUp() {
		$this->sql_return = array(array('id' => 1, 'name' => 'john'));
		$this->zend_sql = $this->getMockBuilder('Zend\Db\Sql\Sql')->disableOriginalConstructor()->getMock();
		$this->sql = $this->getMockBuilder('Zend\Db\Sql\SqlInterface')->disableOriginalConstructor()->getMock();
		$this->result = $this->getMockBuilder('Zend\Db\ResultSet\ResultSet')->disableOriginalConstructor()->getMock();
		$this->adapter = $this->getMockBuilder('Zend\Db\Adapter\Adapter')->disableOriginalConstructor()->getMock();
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbConnection::fetchArray
	 */
	function testFetchArray() {
		$this->result->expects($this->once())->method('toArray')->will($this->returnValue($this->sql_return));
		$this->adapter->expects($this->once())->method('query')->will($this->returnValue($this->result));
		$this->zend_sql->expects($this->once())->method('getSqlStringForSqlObject');

		$conn = new ZendDbConnection($this->adapter);
		$conn->setSql($this->zend_sql);
		$this->assertEquals($this->sql_return, $conn->fetchArray($this->sql));

	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbConnection::fetchField
	 */
	function testFetchField() {

		$this->result->expects($this->once())->method('toArray')->will($this->returnValue($this->sql_return));
		$this->adapter->expects($this->once())->method('query')->will($this->returnValue($this->result));
		$this->zend_sql->expects($this->once())->method('getSqlStringForSqlObject');

		$conn = new ZendDbConnection($this->adapter);
		$conn->setSql($this->zend_sql);
		$this->assertEquals(1, $conn->fetchField($this->sql));
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbConnection::fetchColumn
	 */
	function testFetchColumn() {

		$this->result->expects($this->once())->method('toArray')->will($this->returnValue($this->sql_return));
		$this->adapter->expects($this->once())->method('query')->will($this->returnValue($this->result));
		$this->zend_sql->expects($this->once())->method('getSqlStringForSqlObject');

		$conn = new ZendDbConnection($this->adapter);
		$conn->setSql($this->zend_sql);
		$this->assertEquals(current($this->sql_return), $conn->fetchColumn($this->sql));
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbConnection::execute
	 */
	function testExecuteString() {
		$adapter = $this->getMockBuilder('Zend\Db\Adapter\Adapter')->disableOriginalConstructor()->getMock();
		$adapter->expects($this->once())->method('query')->with('SELECT', 'execute');

		$conn = new ZendDbConnection($adapter);
		$conn->execute('SELECT');

	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbConnection::execute
	 */
	function testExecuteSqlInterface() {
		$this->adapter->expects($this->once())->method('query')->with('SELECT', 'execute');
		$this->zend_sql->expects($this->once())->method('getSqlStringForSqlObject')->will($this->returnValue('SELECT'));

		$conn = new ZendDbConnection($this->adapter);
		$conn->setSql($this->zend_sql);
		$conn->execute($this->getMock('Zend\Db\Sql\SqlInterface'));

	}

}