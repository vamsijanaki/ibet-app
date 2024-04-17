<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\Searchable;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use Searchable, UserNotify;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance',
        'kyc_data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address'           => 'object',
        'kyc_data'          => 'object',
        'ver_code_send_at'  => 'datetime',
    ];

    public function loginLogs() {
        return $this->hasMany( UserLogin::class );
    }

    public function transactions() {
        return $this->hasMany( Transaction::class )->orderBy( 'id', 'desc' );
    }

    public function deposits() {
        return $this->hasMany( Deposit::class )->where( 'status', '!=', Status::PAYMENT_INITIATE );
    }

    public function withdrawals() {
        return $this->hasMany( Withdrawal::class )->where( 'status', '!=', Status::PAYMENT_INITIATE );
    }

    public function refBy() {
        return $this->belongsTo( User::class, 'ref_by' );
    }

    public function referrals() {
        return $this->hasMany( User::class, 'ref_by' );
    }

    public function allReferrals() {
        return $this->referrals()->with( 'refBy' );
    }

    public function commissions() {
        return $this->hasMany( CommissionLog::class, 'to_id' )->orderBy( 'id', 'desc' );
    }

    // Attribute

    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }

    // SCOPES
    public function scopeActive( $query ) {
        return $query->where( 'status', Status::USER_ACTIVE )->where( 'ev', Status::VERIFIED )->where( 'sv', Status::VERIFIED );
    }

    public function scopeBanned( $query ) {
        return $query->where( 'status', Status::USER_BAN );
    }

    public function scopeEmailUnverified( $query ) {
        return $query->where( 'ev', Status::NO );
    }

    public function scopeMobileUnverified( $query ) {
        return $query->where( 'sv', Status::NO );
    }

    public function scopeKycUnverified( $query ) {
        return $query->where( 'kv', Status::KYC_UNVERIFIED );
    }

    public function scopeKycPending( $query ) {
        return $query->where( 'kv', Status::KYC_PENDING );
    }

    public function scopeEmailVerified( $query ) {
        return $query->where( 'ev', Status::VERIFIED );
    }

    public function scopeMobileVerified( $query ) {
        return $query->where( 'sv', Status::VERIFIED );
    }

    public function scopeWithBalance( $query ) {
        return $query->where( 'balance', '>', 0 );
    }

}
