<?php
namespace crm;

/**
 * PSR-4 совместимый class loader.
 *
 * See http://www.php-fig.org/psr/psr-4/
 * перевод стандарта psr-4: http://svyatoslav.biz/misc/psr_translation/#_PSR-4
 *
 * В целях расширения возможностей автолоадера,
 * оставляю возможность регистрации пустого неймспейса:
 *     $loader->addPrefix('',  $baseDir);
 * Переработано
 *
 * @author Друшка Александр <fenix-site@yandex.ru>
 */
class Autoloader
{
    /**
     * @var array
     */
    private $prefixes = array();

    /**
     * @param string $prefix
     * @param string $baseDir
     * @param bool $prepend добавить не в конец, а в начало списка префиксов.
     */
    public function addPrefix($prefix, $baseDir, $prepend = false)
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        // Не ассоциативный массив для того, чтобы можно было для одного нэймспейса
        // указывать несколько директорий.
        // Возможно, будет более производительно, если сделать
        // все-таки ассоциативный массив,
        // но каждому нэймспейсу будет соответствовать не строка, а
        // массив строк с директориями.
        $item = array($prefix, $baseDir);

        // retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift($this->prefixes, $item);
        } else {
            array_push($this->prefixes, $item);
        }
    }

    /**
     * @param string $class
     *
     * @return string|null
     */
    private function findFile($class)
    {
        // Унифицирую имя класса, чтобы всегда начиналось с обратного слеша.
        // Добавляю, а не удаляю для того,
        // чтобы использовать для автозагрузки классов без указания нэймспейса.
        if (substr($class, 0, 1) !== '\\') {
            $class = '\\' . $class;
        }

        foreach ($this->prefixes as $current) {

            list($currentPrefix, $currentBaseDir) = $current;

            // Унифицирую префикс нэймспейса, чтобы всегда начиналось с обратного слеша, как имя класса.
            if (substr($currentPrefix, 0, 1) !== '\\') {
                $currentPrefix = '\\' . $currentPrefix;
            }

            if (0 === strpos($class, $currentPrefix)) {
                $classWithoutPrefix = substr($class, strlen($currentPrefix));
                $file = $currentBaseDir .
                    str_replace('\\', DIRECTORY_SEPARATOR, $classWithoutPrefix) . '.php';
                if (is_file($file) && is_readable($file)) {
                    return $file;
                }
            }
        }
        return null;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function loadClass($class)
    {
        $file = $this->findFile($class);
        if (null !== $file) {
            require_once $file;
            return true;
        }
        return false;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array(
            $this,
            'loadClass'
        ), true, $prepend);
    }

    /**
     * Removes this instance from the registered autoloaders.
     */
    public function unregister()
    {
        spl_autoload_unregister(array(
            $this,
            'loadClass'
        ));
    }
}
