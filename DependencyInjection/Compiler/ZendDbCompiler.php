<?php

namespace espend\ZendDbBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class ZendDbCompiler implements CompilerPassInterface {

	protected $driver_mappings = array(
		'pdo_mysql' => 'mysqli',
		'pdo_sqlite' => 'pdo',
		'pdo_pgsql' => 'pgsql',
	);

	protected $platform_map = array(
		'pdo_mysql' => 'Mysql',
		'pdo_sqlite' => 'Sqlite',
		'pdo_pgsql' => 'Postgresql',
	);

	public function process(ContainerBuilder $container) {

		$settings['database']['default'] = array(
			'host' => $this->getParameter($container, 'database_host'),
			'port' => $this->getParameter($container, 'database_port'),
			'name' => $this->getParameter($container, 'database_name'),
			'user' => $this->getParameter($container, 'database_user'),
			'password' => $this->getParameter($container, 'database_password'),
			'driver' => $this->getParameter($container, 'database_driver', 'pdo'),
		);

		$conn = new Definition('espend\ZendDbBundle\Zend\Db\ZendDbManager');
		$conn->addArgument(new Reference('doctrine.orm.entity_manager'));

		foreach ($settings['database'] as $key => $database) {

			if (!isset($this->driver_mappings[$database['driver']])) {
				throw new RuntimeException('invalid driver map on ' . $database['driver']);
			}

			if (!isset($this->platform_map[$database['driver']])) {
				throw new RuntimeException('invalid platform map on ' . $database['driver']);
			}

			$args = array(
				'username' => !isset($database['user']) ? : $database['user'],
				'host' => $database['host'],
				'database' => $database['name'],
				'driver' => $this->driver_mappings[$database['driver']],
				'platform' => $this->platform_map[$database['driver']],
			);

			if (isset($database['password'])) {
				$args['password'] = $database['password'];
			}

			if (isset($database['port'])) {
				$args['port'] = $database['port'];
			}

			$def = new Definition();
			$def->setClass('Zend\Db\Adapter\Adapter');
			$def->addArgument($args);

			$container->setDefinition('zend.db.adapter.' . $key, $def);

			if ($key == 'default') {
				$container->setAlias('zend.db.adapter', 'zend.db.adapter.' . $key);
			}

			$conn->addMethodCall('addAdapter', array($key, new Reference('zend.db.adapter.' . $key)));

		}

		$container->setDefinition('zend.db.manager', $conn);
	}

	private function getParameter(ContainerBuilder $container, $parameter, $default = null) {
		if (!$container->hasParameter($parameter)) {
			return $default;
		}

		return $container->getParameter($parameter);
	}
}