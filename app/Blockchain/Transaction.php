<?php

namespace App\Blockchain;

class Transaction
{
    public $from;
    public $to;
    public $amount;
    public $signature;

    public function __construct(?string $from, string $to, int $amount, string $signature = '')
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        $this->signature = $signature;
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
        return ($this->from ? substr($this->from, 72, 7) : 'NONE').'->'.$this->to;
    }

    public function isValid()
    {
        return !$this->from || Wallet::isValid($this->calculateHash(), $this->signature, $this->from);
    }
}
