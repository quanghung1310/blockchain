<?php

namespace App\Blockchain;

use Carbon\Carbon;
use League\Flysystem\Exception;

class BlockChain
{
    /**
     * @var Block[]
     */
    public $chain = [];

    /**
     * @var Transaction[]
     */
    public $pendingTransactions = [];

    private $difficult = 1;

    private $miningReward = 1;

    public function __construct()
    {
        $this->chain[] = $this->genesisBlock();
    }

    protected function genesisBlock()
    {
        return new Block('Genesis block', Carbon::now()->format('Y-m-d'), '0');
    }

    protected function getLatestBlock()
    {
        return $this->chain[count($this->chain) - 1];
    }

    public function minePendingTransactions($miningRewardAddress)
    {
        $transaction = new Transaction(null, $miningRewardAddress, $this->miningReward);
        $this->pendingTransactions[] = $transaction;
        $block = new Block($this->pendingTransactions, Carbon::now()->format('Y-m-d'), $this->getLatestBlock()->hash);
        $block->mineBlock($this->difficult);

        $this->chain[] = $block;

        $this->pendingTransactions = [];
    }

    public function addTransaction($transaction)
    {
        if (!$transaction->isValid()) {
            abort(500, 'Cannot add invalid transaction');
        }

        $this->pendingTransactions[] = $transaction;
    }

    public function getBalanceOfAddress($address)
    {
        $balance = 0;

        foreach ($this->chain as $block) {
            if (is_array($block->transactions)) {
                foreach ($block->transactions as $transaction)
                {
                    if ($transaction->from === $address) {
                        $balance -= $transaction->amount;
                    }

                    if ($transaction->to === $address) {
                        $balance += $transaction->amount;
                    }
                }
            }
        }

        return $balance;
    }

    public function isChainValid()
    {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];

            if (!$currentBlock->hasValidTransactions()) {
                return false;
            }

            if ($currentBlock->previousHash !== $previousBlock->calculateHash()) {
                return false;
            }

            if ($previousBlock->hash !== $previousBlock->calculateHash()) {
                return false;
            }
        }

        return true;
    }
}
