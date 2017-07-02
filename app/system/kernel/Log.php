<?php
namespace crm;

/**
 * Базовый функционал системы. Логирование.
 * crm
 * @author Друшка Александр <fenix-site@yandex.ru>
 */
class Log {

	static private $log_path = COMMON_LOG;
	static private $error_path = ERROR_LOG;
	//По умолчанию добавляет запись в конец файла лога
	static private $file_open_flag = "a+";
	

	/**
	 * Удаляет содержимое файла.
	 * Возвращает true, если всё прошло успешно. Записывает время очистки файла.
	 * TODO: Хорошо бы добавить и автора очистки.
	 * @param string log_path Путь к файлу лога. Обязательный параметр.
	 * @return boolean
	 */
	static public function clear_file($log_path){
		if (!file_exists($log_path))
			return false;
		$handle = fopen($log_path, "w");
		if(!$handle)
			return false;
		fwrite($handle, "Выполенена очистка файла: " . date("Y-m-d H:i:s") . "\n"
				. "//----------------------------------------------------\n");
		fclose($handle);
		return true;
	}

	
	/**
	 * Простое логирование по заданному пути.
	 * Логирование исключительно для строки message
	 * Если путь не задан, логирует в общий файл common_log.txt
	 * Возвращает true в случае успеха или false, если нет доступа
	 * к файлу логирования
	 *
	 * @param string message Строка для логирования
	 * @param string log_path Путь к файлу лога
	 * @return boolean
	 */
	static public function log($message, $log_path = "") {
		if (!$log_path)
			$log_path = self::$log_path;
		if (!file_exists($log_path))
			return false;
		$handle = fopen($log_path, self::$file_open_flag);
		if(!$handle)
			return false;
		
		//Оформляем строку
		$message = "Время записи: " . date("Y-m-d H:i:s") . "\n"
				. $message . "\n"
				. "//----------------------------------------------------\n";
		fwrite($handle, $message);
		fclose($handle);
		return true;
	}

	/**
	 * Простое логирование, для любого типа $message (массив, строка, объект и тд).
	 * Для логирования используется буферизация var_dump($message)
	 * 
	 * Если требуется логировать несколько переменных данным способом,
	 * можно $message представить в виде массива переменных. Но второе значение
	 * обязательно путь к логированию.
	 * Возвращает true при нормальной записи и очищает буфер вывода ob_end_clean().
	 * Если путь не задан, логирует в общий файл log.txt
	 *
     * TODO Почему-то добавление в этом методеы срабатывает дважды. Надо разобратьсяы
     *
	 * @param string|array|object|anytype message Переменная для логирования
	 * @param string log_path Путь к файлу лога
	 * @return boolean
	 */
	static public function ob_log($message, $log_path = ""){
		if (!$log_path)
			$log_path = self::$log_path;
		if (!file_exists($log_path))
			return false;
		$handle = fopen($log_path, self::$file_open_flag);
		if(!$handle)
			return false;

		//Оформляем строку
		ob_start();
		var_dump($message);
		$message = "Время записи: " . date("Y-m-d H:i:s") . "\n"
				. ob_get_contents(). "\n"
				. "//----------------------------------------------------\n";
		fwrite($handle, $message);
		ob_end_clean();
		fclose($handle);
		return true;
	}

	/**
	 * Логирование ошибок.
	 * Если файл не указан, используется общий файл, указанный в конфиге
	 * @param anytype message Сообщение об ошибке любого типа
	 * @param type log_path Путь к файлу
	 * @return type
	 */
	static public function error_log($message, $log_path = "") {
		if(!$log_path)
			$log_path = self::$error_path;
		$ret = self::log($message, $log_path);
		return $ret;
	}

}
