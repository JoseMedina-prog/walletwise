@props(['amount' => 0, 'type' => 'income', 'signed' => true])

@php
    $sign = $signed ? ($type === 'income' ? '+' : '-') : '';
    $class = match($type) {
        'income'  => 'amount-income',
        'expense' => 'amount-expense',
        'positive' => 'amount-positive',
        'negative' => 'amount-negative',
        default   => 'amount-neutral',
    };
@endphp

<span class="{{ $class }}">{{ $sign }}${{ number_format($amount, 2) }}</span>
