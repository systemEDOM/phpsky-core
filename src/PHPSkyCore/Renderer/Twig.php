<?php

namespace PHPSkyCore\Renderer;

use Twig_Loader_Filesystem;
use Twig_Environment;

use PHPSkyCore\Auth\Auth;

class Twig
{

	private static $twig = null;

	/**
     * Init render for Twig
     * @return template
     */
    public static function render($path, $params = array())
    {
        $loader = new Twig_Loader_Filesystem(APP_PATH."/resources/views/");
        
        self::$twig = new Twig_Environment($loader, array("cache" => APP_PATH."/resources/storage/views/", "auto_reload" => true));

        self::addGlobals();
        self::addFunctions();

        echo self::$twig->render($path, $params);
    }

    /**
     * Add the globals functions
     * @return type
     */
    private static function addFunctions()
    {
    	self::$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
            return sprintf('%s', ltrim($asset, '/'));
		}));

        self::$twig->addFunction(new \Twig_SimpleFunction('getBaseUrl', function () {
            return getBaseUrl();
        }));

        self::$twig->addFunction(new \Twig_SimpleFunction('csrf_token', function () {
            return csrf_token();
        }));
    }

    /**
     * Add the globals variables
     * @return type
     */
    private static function addGlobals()
    {
    	self::$twig->addGlobal('session', $_SESSION);
        self::$twig->addGlobal('APP', APP);
        self::$twig->addGlobal('auth', Auth::getAuth());
    } 

    /**
     * Add global variable
     * @return type
     */
    public static function addGlobal($name, $content)
    {
        self::$twig->addGlobal($name, $content);
    }   
}