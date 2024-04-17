<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Session;

class MobileBetSlipCount extends Component
{
    public $betSlipCart = [];

    #[On('game-added'), On('game-removed')]
    public function betSlipUpdate() {
        $this->betSlipCart = Session::get('betSlipCart');
    }

    public function mount() {
        $this->betSlipCart = session()->has('betSlipCart') ? Session::get('betSlipCart') : [];
    }

    public function render()
    {
        return view('livewire.mobile-bet-slip-count');
    }
}
