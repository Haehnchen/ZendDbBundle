<?php

namespace espend\ZendDbBundle\Tests\Zend\Db\Sql;

use espend\ZendDbBundle\Zend\Db\Sql\Select;
use espend\ZendDbBundle\Zend\Db\Sql\Sql;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class SqlTest extends \PHPUnit_Framework_TestCase {

	protected $class_meta;
	protected $em;

	/**
	 * @var \espend\ZendDbBundle\Zend\Db\Sql\Sql
	 */
	protected $sql;

	protected $adapter;

	function setUp() {
		$this->class_meta = new ClassMetadataInfo('Test\Test');
		$this->class_meta->setPrimaryTable(array('name' => 'test'));

		$this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
		$this->em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($this->class_meta));

		$platform = $this->getMockBuilder('Zend\Db\Adapter\Platform\PlatformInterface')->disableOriginalConstructor()->getMock();
		$platform->expects($this->once())->method('getName')->will($this->returnValue('mysql'));

		$this->adapter = $this->getMockBuilder('Zend\Db\Adapter\AdapterInterface')->disableOriginalConstructor()->getMock();
		$this->adapter->expects($this->once())->method('getPlatform')->will($this->returnValue($platform));

	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Sql::select
	 */
	function testSelect() {
		$select = new Sql($this->adapter);
		$select->setObjectManager($this->em);

		$this->assertEquals('SELECT "test".* FROM "test"', $select->select('Test\Test')->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Sql::select
	 */
	function testSelectCamelize() {

		$this->class_meta = new ClassMetadataInfo('Test\TestCamelize');
		$this->class_meta->setPrimaryTable(array('name' => 'test'));

		$this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
		$this->em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($this->class_meta));

		$select = new Sql($this->adapter);
		$select->setObjectManager($this->em);

		$this->assertEquals('SELECT "test_camelize".* FROM "test" AS "test_camelize"', $select->select('Test\TestCamelize')->getSqlString());

	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Sql::delete
	 */
	function testDelete() {
		$sql = new Sql($this->adapter);
		$sql->setObjectManager($this->em);
		$this->assertEquals('DELETE FROM "test"', $sql->delete('Test\TestCamelize')->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Sql::update
	 */
	function testUpdate() {
		$sql = new Sql($this->adapter);
		$sql->setObjectManager($this->em);
		$this->assertEquals('UPDATE "test" SET ', $sql->update('Test\TestCamelize')->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Sql::insert
	 */
	function testInsert() {
		$sql = new Sql($this->adapter);
		$sql->setObjectManager($this->em);
		$this->assertEquals('INSERT INTO "test" () VALUES ()', $sql->insert('Test\TestCamelize')->getSqlString());
	}

}