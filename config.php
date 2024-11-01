<?php

use SmartemailingDeps\DI\Definition\Helper\CreateDefinitionHelper;
use SmartemailingDeps\Wpify\CustomFields\CustomFields;
use SmartemailingDeps\Wpify\PluginUtils\PluginUtils;

return array(
	CustomFields::class    => ( new CreateDefinitionHelper() )
		->constructor( plugins_url( 'deps/wpify/custom-fields', __FILE__ ) ),
	PluginUtils::class     => ( new CreateDefinitionHelper() )
		->constructor( __DIR__ . '/smartemailing.php' ),
	\SmartemailingDeps\Wpify\Model\Manager::class     => ( new CreateDefinitionHelper() )
	->constructor( [] )
);
