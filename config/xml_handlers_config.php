<?php

return [
	//
	'root' => [
		'namespace' => [
			'\\CLI\\XMLParser\\Files\\ByRoot\\',
		],

		'handlers' => [
			'AS_HOUSE_TYPES',
			'AS_ADDHOUSE_TYPES',
			'AS_OBJECT_LEVELS',
		],
	],

	'regions' => [
		'namespace' =>[
			'\\CLI\\XMLParser\\Files\\ByRegions\\',
		],

		'handlers' => [
			'AS_ADDR_OBJ',
			'AS_HOUSES',
			'AS_ADDR_OBJ_PARAMS',
			'AS_MUN_HIERARCHY',
		],
	],
];