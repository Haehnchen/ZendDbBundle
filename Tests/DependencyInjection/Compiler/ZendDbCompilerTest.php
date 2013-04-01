<?php

namespace espend\ZendDbBundle\Tests\DependencyInjection\Compiler;

use espend\ZendDbBundle\DependencyInjection\Compiler\ZendDbCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ZendDbCompilerTest extends \PHPUnit_Framework_TestCase {

	/** @var ContainerBuilder */
	private $container;

	/** @var ZendDbCompiler */
	protected $compiler;

	function setUp() {
		$this->container = new ContainerBuilder();

		$this->container->setParameter('database_host', '192.168.0.1');
		$this->container->setParameter('database_port', '3333');
		$this->container->setParameter('database_name', 'db_name');
		$this->container->setParameter('database_user', 'username');
		$this->container->setParameter('database_password', 'password');
		$this->container->setParameter('database_driver', 'pdo_mysql');

		$this->compiler = new ZendDbCompiler();
	}

	function databaseProvider() {
		return array(
			array('pdo_mysql', 'mysqli', 'Mysql'),
			array('pdo_sqlite', 'pdo', 'Sqlite'),
			array('pdo_pgsql', 'pgsql', 'Postgresql'),
		);
	}

	/**
	 * @dataProvider databaseProvider
	 * @covers espend\ZendDbBundle\DependencyInjection\Compiler\ZendDbCompiler::process
	 */
	function testContainer($doctrine_driver, $zend_driver, $zend_platform) {

		$this->container->setParameter('database_driver', $doctrine_driver);
		$this->compiler->process($this->container);
		$def = $this->container->getDefinition('zend.db.adapter.default');

		$this->assertEquals(Array(
			'username' => 'username',
			'host' => '192.168.0.1',
			'database' => 'db_name',
			'driver' => $zend_driver,
			'platform' => $zend_platform,
			'password' => 'password',
			'port' => 3333,
		), $def->getArgument(0));

	}

	/**
	 * @covers espend\ZendDbBundle\DependencyInjection\Compiler\ZendDbCompiler::process
	 */
	function testServiceAndDefaultAlias() {
		$this->compiler->process($this->container);
		$this->assertTrue($this->container->has('zend.db.adapter'));
		$this->assertTrue($this->container->has('zend.db.adapter.default'));
	}

	/**
	 * @expectedException \Symfony\Component\DependencyInjection\Exception\RuntimeException
	 * @covers espend\ZendDbBundle\DependencyInjection\Compiler\ZendDbCompiler::process
	 */
	function testInvalidDriver() {
		$this->container->setParameter('database_driver', 'unknown');
		$this->compiler->process($this->container);
	}


}