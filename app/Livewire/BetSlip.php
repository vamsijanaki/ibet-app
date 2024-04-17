<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;
use Session;
use Livewire\Attributes\On;

class BetSlip extends Component
{
    public $betSlipCart = [];

    public $showBetSlip;

    #[On('game-added'), On('game-removed')]
    public function betSlipUpdate() {
        $this->betSlipCart = Session::get('betSlipCart');
    }

    public function deSelectGame($game_id) {
        $game = $this->betSlipCart;
        unset($game[$game_id]);
        Session::put('betSlipCart', $game);
        $this->betSlipCart = Session::get('betSlipCart');

        $this->dispatch('game-removed')->to(League::class);
        $this->dispatch('game-removed')->to(MobileBetSlipCount::class);
    }

    public function clearBetSlip() {
        Session::put('betSlipCart', []);
        $this->betSlipCart = Session::get('betSlipCart');
        $this->showBetSlip = false;
        $this->dispatch('toggle-betslip')->to(League::class);
        $this->dispatch('game-removed')->to(League::class);
        $this->dispatch('game-removed')->to(MobileBetSlipCount::class);
    }

    public function selectGame($game_id, $type) {
        $game = $this->betSlipCart;
        if(count($game) >= 5 && !isset( $this->betSlipCart[ $game_id ] ) ) {
            //session()->flash('message', 'Maximum 5 bet.');
            // Trigger js to show toast
            $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Maximum 5 bet.']);
            return false;
        }
        if(isset($this->betSlipCart[$game_id])){
            $game[$game_id] = $type;
        } else {
            $game[$game_id] = $type;
        }

        Session::put('betSlipCart', $game);
        $this->betSlipCart = Session::get('betSlipCart');

        $this->dispatch('game-added')->to(League::class);
        $this->dispatch('game-added')->to(MobileBetSlipCount::class);
    }

    public function selectH2h($game_id, $player_id) {
        $game = $this->betSlipCart;

        if(count($game) >= 5 && !isset( $this->betSlipCart[ $game_id ] ) ) {
           // session()->flash('message', 'Maximum 5 bet.');
            // Trigger js to show toast
            $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Maximum 5 bet.']);
            return false;
        }
        if(isset($this->betSlipCart[$game_id])){
            if ($game[$game_id] == $player_id){
                unset($game[$game_id]);
            } else {
                $game[$game_id] = $player_id;
            }
        } else {
            $game[$game_id] = $player_id;
        }

        Session::put('betSlipCart', $game);
        $this->betSlipCart = Session::get('betSlipCart');

        $this->dispatch('game-added')->to(League::class);
        $this->dispatch('game-added')->to(MobileBetSlipCount::class);
    }

    public function mount() {
        $this->betSlipCart = session()->has('betSlipCart') ? Session::get('betSlipCart') : [];
    }

    public function render()
    {
        return view('livewire.bet-slip');
    }
}
