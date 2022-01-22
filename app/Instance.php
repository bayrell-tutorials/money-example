<?php

/*!
 *
 * MIT License
 * 
 * Copyright (c) 2020 - 2021 "Ildar Bikmamatov" <support@bayrell.org>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App;


class Instance extends \TinyPHP\App
{
	
	/**
	 * Init app
	 */
	public function init()
	{
		parent::init();
		
		/* Include routes */
		$this->addRoute(\App\Routes\MoneyRoute::class);
		
		/* Includes models */
		$this->addModel(\App\Models\Money::class);
		$this->addModel(\App\Models\History::class);

		/* Includes console commands */
		$this->addConsoleCommand(\App\Console\UpdateBalance::class);
		$this->addConsoleCommand(\App\Console\UpdateBalanceAll::class);
	}
	
	
	
	/**
	 * Console app created
	 */
	function consoleAppCreated()
	{
	}
	
	
	
	/**
	 * 404 error
	 */
	function actionNotFound($container)
	{
		$container->render("@app/404.twig");
		$container->response->setStatusCode(404);
		return $container;
	}
	
	
	
	/**
	 * Method not allowed
	 */
	function actionNotAllowed($container)
	{
		$container->render("@app/405.twig");
		$container->response->setStatusCode(405);
		return $container;
	}
	
	
	
	/**
	 * Request before, after
	 */
	function request_before($container){}
	function request_after($container){}
}