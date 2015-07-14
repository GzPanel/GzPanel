<?php
namespace api\Entities;

class TokensEntity
{
    public $Generated;
    public $Creation;
    public $Type;
    public $Linked;

    public function getArrayCopy()
    {
        return array(
            'Generated' => $this->Generated,
            'Creation' => $this->Creation,
            'Type' => $this->Type,
            'Linked' => $this->Linked,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->Generated = $array['Generated'];
        $this->Creation = $array['Creation'];
        $this->Type = $array['Type'];
        $this->Linked = $array['Linked'];
    }
}
