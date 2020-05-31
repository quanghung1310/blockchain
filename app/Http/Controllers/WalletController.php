<?php

namespace App\Http\Controllers;

use App\Blockchain\BlockChain;
use App\Blockchain\Transaction;
use App\Blockchain\Wallet;

class WalletController extends Controller
{
    const db = 'wallets.json';
    const dbTransaction = 'transactions.json';

    public function readWallet()
    {
        if (file_exists(self::db)) {
            $wallets = json_decode(file_get_contents(self::db), true);
        }

        return $wallets ?? [];
    }

    public function readTransaction()
    {
        if (file_exists(self::dbTransaction)) {
            $transactions = json_decode(file_get_contents(self::dbTransaction), true);
        }

        return $transactions ?? [];
    }

    public function create()
    {
        $wallet = Wallet::generateKeyPair();
        $db = $this->readWallet();
        $db[] = $wallet;
        file_put_contents(self::db, json_encode($db));

        return response()
            ->json($wallet);
    }

    public function transaction()
    {
        $this->validate($this->request(), [
            'from' => 'required',
            'to' => 'required',
            'amount' => 'required',
        ]);
        $from = $this->request()->get('from');
        $to = $this->request()->get('to');
        $amount = $this->request()->get('amount');

        $db = $this->readWallet();

        $private = null;

        foreach ($db as $item) {
            if ($item['public'] === $from) {
                $private = $item['private'];
            }
            $wallet[] = $item;
        }

        if (is_null($private)) {
            abort(400, 'Not found address');
        }

        $dbTransaction = $this->readTransaction();
        if (empty($dbTransaction)) {
            $coin = new BlockChain();
            $coin->genesisBlock();
        } else {
            $coin = new BlockChain();
            $coin->setChain($dbTransaction['chain']);
        }

        $tx1 = new Transaction($from, $to, $amount);
        $tx1->signTransaction($private);
        $coin->addTransaction($tx1);
        $coin->minePendingTransactions('System');
        $dbTransaction = $coin;

        file_put_contents(self::dbTransaction, json_encode($dbTransaction));

        return $this->readTransaction();
    }

    public function listWallets()
    {
        $db = $this->readWallet();
        $wallet = [];
        foreach ($db as $item) {
            unset($item['private']);
            $wallet[] = $item;
        }

        return response()
            ->json($wallet);
    }
}
