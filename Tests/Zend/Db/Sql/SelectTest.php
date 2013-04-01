<?php

namespace espend\ZendDbBundle\Tests\Zend\Db\Sql;

use Doctrine\ORM\Mapping\ClassMetadata;
use espend\ZendDbBundle\Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Expression;

class SelectTest extends \PHPUnit_Framework_TestCase {

	protected $class_meta;
	protected $em;

	function setUp() {
		$this->class_meta = new ClassMetadata('Test:Test');
		$this->class_meta->setPrimaryTable(array('name' => 'my_test'));

		$this->em = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$this->em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($this->class_meta));
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::columns
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableName
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableNameWithAlias
	 */
	function testSelectShortcut() {
		$select = new Select('Test:Test', $this->em);
		$this->assertEquals('SELECT "test".* FROM "my_test" AS "test"', $select->getSqlString());

		$select->columns(array(
			'day' => new Expression("date(created_at)")
		));

		$this->assertEquals('SELECT date(created_at) AS "day" FROM "my_test" AS "test"', $select->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::from
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableName
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableNameWithAlias
	 */
	function testEqualAlias() {

		$this->class_meta = new ClassMetadata('Test\Entity\Test');
		$this->class_meta->setPrimaryTable(array('name' => 'test'));

		$this->em = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$this->em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($this->class_meta));

		$select = new Select('Test\Entity\Test', $this->em);
		$this->assertEquals('SELECT "test".* FROM "test"', $select->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::from
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableName
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableNameWithAlias
	 */
	function testSelectArrayEntity() {

		$this->class_meta = new ClassMetadata('Test\Entity\Test');
		$this->class_meta->setPrimaryTable(array('name' => 'test'));

		$this->em = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$this->em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($this->class_meta));

		$select = new Select(array('Test\Entity\Test' => 'table_alias'), $this->em);
		$this->assertEquals('SELECT "table_alias".* FROM "test" AS "table_alias"', $select->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::join
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableName
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableNameWithAlias
	 */
	function testJoinEntity() {

		$this->class_meta = new ClassMetadata('Test\Entity\TestBla');
		$this->class_meta->setPrimaryTable(array('name' => 'test'));

		$this->em = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$this->em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($this->class_meta));

		$select = new Select('table_name', $this->em);
		$select->join('Test\Entity\TestBla', 'table_name.id = test_bla.id');

		$this->assertEquals('SELECT "table_name".*, "test_bla".* FROM "table_name" INNER JOIN "test" AS "test_bla" ON "table_name"."id" = "test_bla"."id"', $select->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::join
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableName
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableNameWithAlias
	 */
	function testJoinEntitySameAlias() {

		$this->class_meta = new ClassMetadata('Test\Entity\TestBla');
		$this->class_meta->setPrimaryTable(array('name' => 'test'));

		$this->em = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$this->em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($this->class_meta));

		$select = new Select('table_name', $this->em);
		$select->join('Test\Entity\Test', 'table_name.id = test_bla.id');

		$this->assertEquals('SELECT "table_name".*, "test".* FROM "table_name" INNER JOIN "test" ON "table_name"."id" = "test_bla"."id"', $select->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::join
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableName
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::getDoctrineTableNameWithAlias
	 */
	function testJoinArrayEntitySameAlias() {

		$this->class_meta = new ClassMetadata('Test\Entity\TestBla');
		$this->class_meta->setPrimaryTable(array('name' => 'test'));

		$this->em = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$this->em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($this->class_meta));

		$select = new Select('table_name', $this->em);
		$select->join(array('test_alias' =>	'Test\Entity\TestBla'), 'table_name.id = test_bla.id');

		$this->assertEquals('SELECT "table_name".*, "test_alias".* FROM "table_name" INNER JOIN "test" AS "test_alias" ON "table_name"."id" = "test_bla"."id"', $select->getSqlString());
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\Sql\Select::from
	 */
	function testSelectPlainZend() {
		$select = new Select('table_name');
		$this->assertEquals('SELECT "table_name".* FROM "table_name"', $select->getSqlString());
	}

}