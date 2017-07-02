<?php
namespace crm;
use crm\SpecialPage;

class Template
{

    private $registry;
    private $vars = array();

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Не перезаписывает переменную, если $overwrite = false
     *
     * @param string $varname
     * @param $value
     * @param bool $overwrite
     * @return bool
     */
    public function set($varname, $value, $overwrite = true)
    {
        if (isset($this->vars[$varname]) == true && $overwrite == false) {
            return false;
        }

        $this->vars[$varname] = $value;
        return true;
    }

    public function remove($varname)
    {
        unset($this->vars[$varname]);
        return true;
    }


    public function render($name)
    {
        $path = VIEW_FOLDER . $name . '.php';
        if (file_exists($path) == false) {
            //Редиректим на 404 страницу
            SpecialPage::redirect404();
        }

        // Load variables
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }
        require "$path";
    }

}
