<?php

namespace App\Http\Controllers;

use App\Blockchain\BlockChain;
use App\Blockchain\Transaction;
use App\Blockchain\Wallet;

class WalletController extends Controller
{
    const db = 'wallets.json';

    public function readDB()
    {
        if (file_exists(self::db)) {
            $wallets = json_decode(file_get_contents(self::db), true);
        }

        return $wallets ?? [];
    }

    public function create()
    {
        $wallet = Wallet::generateKeyPair();
        $db = $this->readDB();
        $db[] = $wallet;
        file_put_contents(self::db, json_encode($db));
        $wallet2 = Wallet::generateKeyPair();
        $db[] = $wallet2;
        file_put_contents(self::db, json_encode($db));

//
//        return view('wallet', ['wallet' => $wallet]);
        $coin = new BlockChain();
        $tx1 = new Transaction($wallet2['public'], 'address2', 20);
        $tx1->signTransaction($wallet2['private']);
        $coin->addTransaction($tx1);

        $tx2 = new Transaction($wallet['public'], $wallet2['public'], 30);
        $tx2->signTransaction($wallet['private']);
        $coin->addTransaction($tx2);

        $coin->minePendingTransactions($wallet['public']);
        \Log::info('Balance test: ', [$coin->getBalanceOfAddress($wallet['public'])]);

        return response()
            ->json($coin);

    }

    public function listWallets()
    {
        return response()
            ->json();
    }
}
