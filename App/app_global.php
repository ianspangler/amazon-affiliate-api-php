<?php

	namespace App;


	/*
		this global function is called automatically whenever a class is instantiated
	*/
	spl_autoload_register(function($class) {
	    if (strpos($class, 'App\\') === 0) {  
	    	//print(getcwd() . '/'.strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php'); exit;
	        require_once getcwd() . '/'.strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';

	    } 
	});

