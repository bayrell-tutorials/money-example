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

namespace App\Routes;


use App\Models\Account;
use App\Models\MoneyException;


class MoneyRoute extends \TinyPHP\Route
{
	
	/**
	 * Routes
	 */
	function routes($routes)
	{
		$routes->addRoute
		(
			['GET'],
			'/',
			[$this, "actionIndex"]
		);
		
		$routes->addRoute
		(
			['POST', 'GET'],
			'/add_money',
			[$this, "actionAddMoney"]
		);
		
		$routes->addRoute
		(
			['POST', 'GET'],
			'/transfer_money',
			[$this, "actionTransferMoney"]
		);
	}
	
	
	
	/**
	 * Action index
	 */
	function actionIndex($container)
	{
		$container->render("@app/index.twig", []);
	}
	
	
	
	/**
	 * Add money
	 */
	function actionAddMoney($container)
	{
		$account_number = $container->post("account_number");
		$description = trim($container->post("description"));
		$money = (double)($container->post("money"));
		
		$data = [
			"account_number" => $account_number,
			"description" => $description,
			"money" => $money,
			"form_result" => [],
			"form_result_class" => "",
		];
		
		if ($container->isPost())
		{
			try
			{
				/* Проверяем аккаунт */
				$account = Account::findByNumber($account_number);
				if (!$account)
				{
					throw new MoneyException("Account not found", MoneyException::ACCOUNT_NOT_FOUND);
				}
				
				/* Проверка на значение */
				if ($money <= 0)
				{
					throw new MoneyException("Money should be more than zero", MoneyException::MONEY_LESS_ZERO);
				}
				
				/* Описание */
				if ($description == "")
				{
					throw new MoneyException("Description is empty", MoneyException::UNKNOWN);
				}
				
				$history = $account->addMoney($money, $description);
				$account->updateBalance();
				
				$data["form_result_class"] = "web_form__result--success";
				$data["form_result"][] = "Transaction id: " . $history["id"];
				$data["form_result"][] = "Success";
			}
			catch (\PDOException $e)
			{
				$data["form_result_class"] = "web_form__result--error";
				$data["form_result"][] = $e->getMessage();
			}
			catch (\App\Models\MoneyException $e)
			{
				$data["form_result_class"] = "web_form__result--error";
				$data["form_result"][] = $e->getMessage();
			}
		}
		
		$container->render("@app/add_money.twig", $data);
	}
	
	
	
	
	/**
	 * Transfer money
	 */
	function actionTransferMoney($container)
	{
		$account_from = $container->post("account_from");
		$account_to = $container->post("account_to");
		$description = $container->post("description");
		$money = (double)($container->post("money"));
		
		$data = [
			"account_from" => $account_from,
			"account_to" => $account_to,
			"description" => $description,
			"money" => $money,
			"form_result" => [],
			"form_result_class" => "",
		];
		
		if ($container->isPost())
		{
			if ($account_from == $account_to)
			{
				$data["form_result_class"] = "web_form__result--error";
				$data["form_result"][] = "Account from equal account to";
			}
			
			else if ($money <= 0)
			{
				$data["form_result_class"] = "web_form__result--error";
				$data["form_result"][] = "Money less than 0";
			}
			
			else
			{
				try
				{
					Account::transferMoney($account_from, $account_to, $money, $description);
					
					$data["form_result_class"] = "web_form__result--success";
					$data["form_result"][] = "Success";
				}
				catch (\PDOException $e)
				{
					$data["form_result_class"] = "web_form__result--error";
					$data["form_result"][] = $e->getMessage();
				}
				catch (\App\Models\MoneyException $e)
				{
					$data["form_result_class"] = "web_form__result--error";
					$data["form_result"][] = $e->getMessage();
				}
			}
		}
		
		$container->render("@app/transfer_money.twig", $data);
	}
	
}