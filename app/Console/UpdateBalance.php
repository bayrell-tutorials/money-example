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

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\Account;


class UpdateBalance extends Command
{
    protected static $defaultName = 'app:money:update_balance';
	
    protected function configure(): void
    {
        $this
			// the short description
			->setDescription('Update balance')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('Update balance')
			
			
			->addArgument('account_number', InputArgument::REQUIRED, 'Account number')
		;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$account_number = $input->getArgument('account_number');
		
		$output->writeln("Update account balance: " . $account_number);
		
		$account = Account::findByNumber($account_number);
		if ($account)
		{
			$account->updateBalance();
		}
		else
		{
			$output->writeln("Account not found");
		}
        return Command::SUCCESS;
    }
}