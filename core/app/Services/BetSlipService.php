<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Facades\Session;

class BetSlipService
{
    protected $myBetSlip;

    // Max Bets
    protected $maxBets = 5;

    // Define Instance
    public $instance;

    public function __construct($instance = null)
    {
        $this->instance = $instance;
        $this->myBetSlip = Session::get('myBetSlip', []);
    }

    // Method to add a bet to the bet slip
    public function addBet($gameId, $betType)
    {

        // Check if the bet slip is full
        if (count($this->myBetSlip) >= $this->maxBets) {
        //    $this->instance->dispatch('show-toast', ['type' => 'info', 'message' => 'Maximum 5 bet.']);
        }

        // Check if the bet already exists
        if (isset($this->myBetSlip[$gameId])) {
            return false;
        }

        // Add the bet to the bet slip
        $this->myBetSlip[$gameId] = $betType;

        // Save the bet slip
        $this->saveBetSlip();

        return true;
    }

    // Method to remove a bet from the bet slip
    public function removeBet($gameId)
    {
        // Check if the bet exists
        if (!isset($this->myBetSlip[$gameId])) {
            return false;
        }

        // Remove the bet from the bet slip
        unset($this->myBetSlip[$gameId]);

        // Save the bet slip
        $this->saveBetSlip();

        return true;
    }

    // Method to save the bet slip
    public function saveBetSlip()
    {
        Session::put('myBetSlip', $this->myBetSlip);
    }

    // Method to get the bet slip
    public function getBetSlip()
    {
        return $this->myBetSlip;
    }

    // Method to clear the bet slip
    public function clearBetSlip()
    {
        Session::forget('myBetSlip');
    }


}