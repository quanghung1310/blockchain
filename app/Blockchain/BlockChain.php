<?php

namespace App\Blockchain;

class BlockChain implements \Countable
{

    public $blocks = [];

    public function __construct($pubKey, $privKey, $amount)
    {
        $this->blocks[] = Block::createGenesis($pubKey, $privKey, $amount);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->blocks);
    }

    public function add(Transaction $transaction)
    {
        $this->blocks[] = new Block($transaction, $this->blocks[count($this->blocks) - 1]->hash);
    }

    public function isValid(): bool
    {
        foreach ($this->blocks as $i => $block) {
            if (!$block->isValid()) {
                return false;
            }

            if ($i != 0 && $this->blocks[$i - 1]->hash != $block->previous) {
                return false;
            }
        }

        return $this->areSpendValid();
    }

    private function areSpendValid()
    {
        foreach ($this->computeBalances() as $pubKey => $amount) {
            if ($amount < 0) {
                 return false;
            }
        }

        return true;
    }

    public function computeBalances()
    {
        $genesisTransaction = $this->blocks[0]->transaction;
        $balances = [$genesisTransaction->to => $genesisTransaction->amount];
        foreach ($this->blocks as $i => $block) {
            if (0 === $i) {
                continue;
            }

            if (!isset($balances[$block->transaction->from])) {
                $balances[$block->transaction->from] = 0;
            }
            $balances[$block->transaction->from] -= $block->transaction->amount;

            if (!isset($balances[$block->transaction->to])) {
                $balances[$block->transaction->to] = 0;
            }
            $balances[$block->transaction->to] += $block->transaction->to;
        }

        return $balances;
    }
}
