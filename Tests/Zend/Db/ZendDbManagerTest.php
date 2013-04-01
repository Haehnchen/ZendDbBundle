<?php

namespace espend\ZendDbBundle\Tests\Zend\Db;

use Zend\Db\Adapter\Adapter;
use espend\ZendDbBundle\Zend\Db\ZendDbManager;

class ZendDbManagerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var ZendDbManager
	 */
	private $manager;

	function setUp() {
		$em = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
		$mysql = $this->getMockBuilder('Zend\Db\Adapter\Driver\Mysqli\Mysqli')->disableOriginalConstructor()->getMock();

		$this->manager = new ZendDbManager($em);
		$this->manager->addAdapter('default', new Adapter(array(
			'host' => '127.0.0.1',
			'driver' => $mysql,
			'port' => '21',
			'name' => 'database',
			'user' => 'user',
			'password' => 'password',
		)));
	}

	/**
	 * @covers espend\ZendDbBundle\Zend\Db\ZendDbManager::getManager
	 */
	function testDriverAndPlatform() {
		$this->assertInstanceOf('Zend\Db\Adapter\AdapterInterface', $this->manager->getManager()->getAdapter());
		$this->assertInstanceOf('Zend\Db\Adapter\Platform\PlatformInterface', $this->manager->getManager()->getAdapter()->getPlatform());
	}

}