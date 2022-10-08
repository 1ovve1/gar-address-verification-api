<?php declare(strict_types=1);

namespace GAR\Storage\Builders;

enum ItemTypes: string
{
	case ITEM = 'item';
	case PARENT = 'parent';
	case HOUSES = 'houses';
	case VARIANT = 'variants';
}