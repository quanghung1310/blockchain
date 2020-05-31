<?php
namespace App\Blockchain;

class Block
{
    public $transactions;
    public $timestamp;
    public $previousHash;
    public $hash;
    private $nonce = 0;

    public function __construct($transactions, $timestamp, $previousHash = '')
    {
        $this->transactions = $transactions;
        $this->timestamp = $timestamp;
        $this->previousHash = $previousHash;
        $this->hash = $this->calculateHash();
    }

    public function calculateHash()
    {
        return hash('sha256', $this->previousHash.$this->timestamp.json_encode($this->transactions).$this->nonce);
    }

    public function mineBlock($difficult)
    {
        while (substr($this->hash, 0, $difficult) !== str_repeat('0', $difficult)) {
            $this->nonce++;
            $this->hash = $this->calculateHash();
        }

        \Log::info('Block mined: ', [$this->hash]);
    }

    public function hasValidTransactions()
    {
        if (is_array($this->transactions)) {
            foreach ($this->transactions as $transaction) {
                if (!$transaction->isValid()) {
                    return false;
                }
            }
        }

        return true;
    }
}
