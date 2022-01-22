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

namespace App\Models;


class User extends \TinyORM\Model
{
	
	
	/**
	 * Return table name
	 */
	static function getTableName()
	{
		return "users";
	}
	
	
	
	/**
	 * Return list of primary keys
	 */
	static function pk()
	{
		return ["id"];
	}
	
	
	
	/**
	 * Return if auto increment
	 */
	static function isAutoIncrement()
	{
		return true;
	}
	
	
	
	/**
	 * To database
	 */
	static function to_database($data)
	{
		$data = parent::to_database($data);
		
		$data = \TinyPHP\Utils::object_intersect($data, [
			"id",
			"name",
		]);
		
		return $data;
	}
	
	
	
	/**
	 * From database
	 */
	static function from_database($data)
	{
		$data = parent::from_database($data);
		return $data;
	}
}