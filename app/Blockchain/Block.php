<?php
namespace App\Blockchain;

class Block
{
    public $previous;
    public $nonce;
    public $hash;
    public $transaction;

    public function __construct(Transaction $transaction, ?self $previous)
    {
        $this->previous = $previous ? $previous->hash : null;
        $this->transaction = $transaction;
        $this->mine();
    }

    public static function createGenesis(string $pubKey, string $privKey, int $amount)
    {
        return new self(new Transaction(null, $pubKey, $amount, $privKey), null);
    }

    public function mine()
    {
        $data = $this->transaction->message().$this->previous;
        $this->nonce = PoW::findNonce($data);
        $this->hash = PoW::hash($data.$this->nonce);
    }

    public function isValid():bool
    {
        return PoW::isValidNonce($this->transaction->message().$this->previous, $this->nonce);
    }
}
