<?php

return [
	'root' => [
		\CLI\XMLParser\Files\ByRoot\AS_HOUSE_TYPES::class,
		\CLI\XMLParser\Files\ByRoot\AS_ADDHOUSE_TYPES::class,
		\CLI\XMLParser\Files\ByRoot\AS_OBJECT_LEVELS::class,
		\CLI\XMLParser\Files\ByRoot\AS_ADDR_OBJ_TYPES::class,
		\CLI\XMLParser\Files\ByRoot\AS_PARAM_TYPES::class,
	],

	'regions' => [
		\CLI\XMLParser\Files\ByRegions\AS_ADDR_OBJ::class,
		\CLI\XMLParser\Files\ByRegions\AS_HOUSES::class,
		\CLI\XMLParser\Files\ByRegions\AS_ADDR_OBJ_PARAMS::class,
		\CLI\XMLParser\Files\ByRegions\AS_MUN_HIERARCHY::class,

		// these can slowed uploader but its important if you need ALL addresses chains
		\CLI\XMLParser\Files\ByRegions\AS_ADM_HIERARCHY::class
	],
];
