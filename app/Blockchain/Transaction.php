<?php

namespace App\Blockchain;

class Transaction
{
    public $from;
    public $to;
    public $amount;
    public $signature;

    public function __construct(?string $from, string $to, int $amount)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
    }

    public function calculateHash()
    {
        return hash('sha256',$this->from.$this->to.$this->amount);
    }

    public function signTransaction($privateKey)
    {
        $this->signature = Wallet::encrypt($this->calculateHash(), $privateKey);
    }

    public function __toString()
    {
        return ($this->from ? substr($this->from, 72, 7) : 'NONE').'->'.substr($this->to, 72, 7);
    }

    public function isValid()
    {
        return !$this->from || Wallet::isValid($this->calculateHash(), $this->signature, $this->from);
    }
}
