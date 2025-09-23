<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'plan.paytm',
        'iyzipay/callback/*',
        'aamarpay/payment',
        'aamarpay/success/*',
        'cashfree/payments/success',
        'paytr/success',
        'midtrans/callback',
        'paytab-success/*',
        '*/webhook',
        '*/payment/webhook',
        '*/callback',
        '*/success',
        '*/cancel',
        '*/fail',
        'invoice/iyzipay/callback/*',
        'retainer/iyzipay/callback/*'
    ];
}
        
