<?php

namespace espend\ZendDbBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use espend\ZendDbBundle\DependencyInjection\Compiler\ZendDbCompiler;

class espendZendDbBundle extends Bundle {

	public function build(ContainerBuilder $container) {
		$container->addCompilerPass(new ZendDbCompiler());
		parent::build($container);
	}

}
