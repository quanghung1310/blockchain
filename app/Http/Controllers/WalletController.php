<?php

namespace App\Http\Controllers;

use App\Blockchain\Wallet;

class WalletController extends Controller
{
    const db = 'balances.json';

    public function readDB()
    {
        if (file_exists(self::db)) {
            $balances = json_decode(file_get_contents(self::db), true);
        } else {
            $balances = ['admin' => 100000];
            file_put_contents(self::db, json_encode($balances));
        }

        return $balances;
    }

    public function create()
    {
        $wallet = Wallet::generateKeyPair();
        return view('wallet', ['wallet' => $wallet]);
    }

    public function profile($user)
    {
        $balances = $this->readDB();

        return sprintf("User %s has %d coins.", $user, $balances[$user] ?? 0);
    }

    public function transfer($from, $to, $amount)
    {

    }
}
