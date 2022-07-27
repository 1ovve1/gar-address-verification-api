<?php

enum ConfigList
{
	case AsHouseTypes;
	case AsAddhouseTypes;
	case AsObjectLevels;
	case AsAddrObj;
	case AsHouses;
	case AsAddrObjParams;
	case AsMunHierarchy;
}

return ConfigList::cases();