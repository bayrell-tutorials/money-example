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


require_once __DIR__ . "/vendor/autoload.php";


define("ROOT_PATH", __DIR__);


$defs = [
	/* App instance */
	"app" => DI\create(\App\Instance::class),
	"twig" => DI\create(\TinyPHP\Twig::class),
	"db" => DI\create(\TinyORM\MySQLConnection::class),
	
	/* App settings */
	"settings" => function()
	{
		return [
		];
	},
	
	/* Connect to database */
	"connectToDatabase" =>
		function ()
		{
			$db = app("db");
			$db->host = getenv("MYSQL_HOST");
			$db->port = getenv("MYSQL_PORT"); if (!$db->port) $db->port = "3306";
			$db->login = getenv("MYSQL_LOGIN");
			$db->password = getenv("MYSQL_PASSWORD");
			$db->database = getenv("MYSQL_DATABASE");		
			$db->connect();
			
			if (!$db->isConnected())
			{
				echo "Error: " . $db->connect_error . "\n";
				exit(1);
			}
			
			return $db;
		},
	
	/* Other classes */
	\FastRoute\RouteParser::class => DI\create(\FastRoute\RouteParser\Std::class),
	\FastRoute\DataGenerator::class => DI\create(\FastRoute\DataGenerator\GroupCountBased::class),
	\FastRoute\RouteCollector::class => DI\autowire(\FastRoute\RouteCollector::class),
	\FastRoute\Dispatcher::class =>
		function (\Psr\Container\ContainerInterface $c)
		{
			$router = $c->get(\FastRoute\RouteCollector::class);
			return new \FastRoute\Dispatcher\GroupCountBased( $router->getData() );
		},
	
	\TinyPHP\ApiResult::class => DI\create(\TinyPHP\ApiResult::class),
	\TinyPHP\RenderContainer::class => DI\create(\TinyPHP\RenderContainer::class),
	\TinyPHP\FatalError::class => DI\create(\TinyPHP\FatalError::class),
];


/* Build dependency injection */
build_di($defs);
