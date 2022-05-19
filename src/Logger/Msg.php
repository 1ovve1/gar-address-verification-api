<?php declare(strict_types=1);

namespace GAR\Logger;

enum Msg : string 
{
	// database messages	
	case LOG_DB_INIT 			= 'инициализация базы данны...';
	case LOG_DB_TABLE			= 'подключение к таблице через модель';
	case LOG_DB_CREATE			= 'создание таблицы';
	case LOG_DB_WITH_FIELDS	 	= 'с полями';
	case LOG_DB_DROP_CONFIRM 	= ' таблица уже существует. Пересоздать её? [Д/н]: ';

	case LOG_DB_BAD 	= 'ошибка обращения к базе данных';

	// xml reader messages
	case LOG_XML_EXTRACT	= 'подготовка файла ';
	case LOG_XML_READ		= 'чтение файла ';

	case LOG_XML_BAD_EXTRACT	= 'ошибка извлечения файла';
	case LOG_XML_BAD_READ = 'ошибка чтения файла';

	// other messages
	case LOG_LAUNCH   = 'Программа запущена...';
	case LOG_COMPLETE = 'УСПЕШНО!';
	case LOG_WARN     = 'ПРЕДУПРЕЖДЕНИЕ ';
	case LOG_BAD 	   = 'АВАРИЙНОЕ ЗАВЕРШЕНИЕ ПРОГРАММЫ';
}