<?php

return [
	'root' => [
		\CLI\XMLParser\Files\ByRoot\AS_HOUSE_TYPES::class,
		\CLI\XMLParser\Files\ByRoot\AS_ADDHOUSE_TYPES::class,
		\CLI\XMLParser\Files\ByRoot\AS_OBJECT_LEVELS::class
	],

	'regions' => [
		\CLI\XMLParser\Files\ByRegions\AS_ADDR_OBJ::class,
		\CLI\XMLParser\Files\ByRegions\AS_HOUSES::class,
		\CLI\XMLParser\Files\ByRegions\AS_ADDR_OBJ_PARAMS::class,
		\CLI\XMLParser\Files\ByRegions\AS_MUN_HIERARCHY::class,
		\CLI\XMLParser\Files\ByRegions\AS_ADM_HIERARCHY::class
	],
];