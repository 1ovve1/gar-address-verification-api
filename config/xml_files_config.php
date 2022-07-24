<?php

enum ConfigList
{
	case AsHouseTypes;
	case AsAddhouseTypes;
	case AsObjectLevels;
	case AsAddrObj;
	case AsAddrObjParams;
	case AsHouses;
	case AsMunHierarchy;
}

return ConfigList::cases();