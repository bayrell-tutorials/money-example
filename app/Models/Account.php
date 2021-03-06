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

use \TinyPHP\Utils;


class Account extends \TinyORM\Model
{
	
	
	/**
	 * Return table name
	 */
	static function getTableName()
	{
		return "accounts";
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
		
		$data = Utils::object_intersect($data, [
			"id",
			"user_id",
			"account_number",
			"balance",
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
	
	
	
	/**
	 * Поиск акаунта по номеру
	 */
	static function findByNumber($account_number)
	{
		return static::select()
			->filter([
				["account_number", "=", $account_number]
			])
			->limit(1)
			->one()
		;
	}
	
	
	
	/**
	 * Проверяет есть ли на счету достаточно денег
	 */
	function hasMoney($money)
	{
		if ($this && $this["balance"] >= $money)
		{
			return true;
		}
		return false;
	}
	
	
	
	/**
	 * Add money
	 */
	function addMoney($money, $description, $from_account_id = null)
	{
		$money = (double)$money;
		
		$account_id = $this["id"];
		//var_dump($account_id);
		
		/* Добавляем историю */
		$history = new History();
		$history->gmtime = gmdate("Y-m-d H:i:s");
		$history->account_id = $account_id;
		$history->money = $money;
		$history->description = $description;
		$history->from_account_id = $from_account_id;
		
		//var_dump($history->toArray());
		
		$history->save();
		
		/* Проверяем вставлена ли история */
		$history->refresh();
		if (!$history->hasLoaded())
		{
			throw new MoneyException("History does not added", MoneyException::HISTORY_DOES_NOT_ADDED);
		}
		
		return $history;
	}
	
	
	
	/**
	 * Получени остатка баланса на определенную дату
	 */
	function getBalance($gmtime_end = null)
	{
		/* Получаем значение последнего баланса и время его создания */
		$q = Balance::select()
			->filter([
				["account_id", "=", $this["id"]]
			])
			->orderBy("gmtime desc")
			->limit(1)
		;
		
		if ($gmtime_end != null)
		{
			$q->addFilter("gmtime", "<", $gmtime_end);
		}
		
		//$q->debug(true);
		
		$balance = $q->one();
		
		$balance_value = $balance ? $balance->value : 0;
		$balance_gmtime = $balance ? $balance->gmtime : "1970-01-01 00:00:00";
		
		/* Получаем историю всех транзакций с момента последнего баланса */
		$q = History::select()
			->filter([
				["account_id", "=", $this["id"]],
				["gmtime", ">=", $balance_gmtime]
			])
			->orderBy("gmtime asc")
		;
		
		if ($gmtime_end != null)
		{
			$q->addFilter("gmtime", "<", $gmtime_end);
		}
		
		//$q->debug(true);
		
		$cursor = $q->execute();
		
		/* Проходим по каждой истории, начиная с balance_gmtime включительно и изменяем значение баланса */
		while ($history = $cursor->fetch())
		{
			$balance_value += $history->money;
		}
		
		$cursor->close();
		
		return $balance_value;
	}
	
	
	
	/**
	 * Обновление баланса
	 */
	function updateBalance()
	{
		$balance_value = $this->getBalance();
		
		/* Обновление баланса */
		$this->balance = $balance_value;
		$this->save();
	}
	
	
	
	/**
	 * Коммит баланса
	 */
	function commitBalance()
	{
		$time = floor(time() / COMMIT_BALANCE) * COMMIT_BALANCE;
		$gmtime = Utils::dbtime($time);
		
		/* Проверяем есть ли такой коммит */
		$balance = Balance::select()
			->filter([
				["account_id", "=", $this["id"]],
				["gmtime", "=", $gmtime],
			])
			->limit(1)
			->one()
		;
		
		if (!$balance)
		{
			$balance_value = $this->getBalance($gmtime);
			$balance = new Balance();
			$balance->account_id = $this["id"];
			$balance->gmtime = $gmtime;
			$balance->value = $balance_value;
			$balance->save();
		}
	}
	
	
	
	/**
	 * Перевод денег с одного счета на другой
	 */
	static function transferMoney($account_from, $account_to, $money, $description)
	{
		$db = app("db")->get();
		
		/* Аккаунт откуда перечисляем */
		$account_from = static::findByNumber($account_from);
		if (!$account_from)
		{
			throw new MoneyException("Account from not found", MoneyException::ACCOUNT_NOT_FOUND);
		}
		
		/* Аккаунт куда перечисляем */
		$account_to = static::findByNumber($account_to);
		if (!$account_to)
		{
			throw new MoneyException("Account to not found", MoneyException::ACCOUNT_NOT_FOUND);
		}
		
		/* Проверяем хватает ли средств на счету $account_from */
		if (!$account_from->hasMoney($money))
		{
			throw new MoneyException("Account doesn't have enough money", MoneyException::DOES_NOT_HAVE_ENOUGH_MONEY);
		}
		
		/* Начало транзакци */
		$db->beginTransaction();
		
		try
		{
			$account_from->addMoney(-$money, $description, $account_to->id);
			$account_to->addMoney($money, $description, $account_from->id);
			
			/* Если все ок, завершаем транзакции */
			$db->commit();
		}
		catch (Exception $e)
		{
			/* Если ошибка, откатываем транзакции */
			$db->rollback();
			
			/* Если ошибка, то вызываем исключение */
			throw $e;
		}
		
		/* Обновление баланса */
		$account_from->updateBalance();
		$account_to->updateBalance();
	}
}