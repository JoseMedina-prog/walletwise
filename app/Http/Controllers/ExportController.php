<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function transactions(Request $request): StreamedResponse
    {
        $userId = Auth::id();
        $from = $request->query('from') ?: '1900-01-01';
        $to = $request->query('to') ?: '2999-12-31';
        $type = $request->query('type');
        $categoryId = $request->query('category_id');

        $query = Transaction::query()
            ->where('user_id', $userId)
            ->with('category')
            ->whereDate('transaction_date', '>=', $from)
            ->whereDate('transaction_date', '<=', $to)
            ->orderBy('transaction_date')
            ->orderBy('id');

        if (in_array($type, ['income', 'expense'], true)) {
            $query->where('type', $type);
        }
        if ($categoryId && is_numeric($categoryId)) {
            $query->where('category_id', (int) $categoryId);
        }

        $filename = 'walletwise-transactions-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility (Spanish accents)
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Fecha', 'Tipo', 'Categoría', 'Descripción', 'Importe', 'Signo'], ',');

            $query->chunk(200, function ($chunk) use ($out) {
                foreach ($chunk as $tx) {
                    fputcsv($out, [
                        $tx->transaction_date->format('Y-m-d'),
                        $tx->type === 'income' ? 'Ingreso' : 'Gasto',
                        $tx->category->name ?? '',
                        $tx->description ?? '',
                        number_format($tx->amount, 2, '.', ''),
                        $tx->type === 'income' ? '+' : '-',
                    ], ',');
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
