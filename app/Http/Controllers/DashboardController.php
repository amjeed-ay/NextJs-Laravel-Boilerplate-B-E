<?php

namespace App\Http\Controllers;

use App\Models\Admin\Store;
use App\Models\Expense;
use App\Models\Sale;


class DashboardController extends Controller
{
    public function index()
    {
        // $from = request('from', date('Y-m-d'));
        // $to = request('to', date('Y-m-d'));


        if(auth()->user()->isGenAdmin){

            $salesToday =  Sale::whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->get();
            $expenseToday =  Expense::whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->get();


        } elseif(auth()->user()->isAdmin){

            $salesToday =  Sale::where('store_id', auth()->user()->store_id)->whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->get();
            $expenseToday =  Expense::where('store_id', auth()->user()->store_id)->whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->get();

        }
        else{

            $salesToday =  Sale::where('user_id', auth()->user()->id)->whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->get();
            $expenseToday =  Sale::where('user_id', auth()->user()->id)->whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->get();

        }

        $total_cash = $salesToday->sum(fn($sale)=> $sale->payments->where('type','cash')->sum('amount'));
        $total_balance = $salesToday->sum(fn($sale)=> $sale->payments->where('type','balance')->sum('amount'));
        $total_expenses = $expenseToday->sum(fn($q)=> $q->items->sum(fn($it)=> $it->price * $it->qty));


        $storesSales = [];

        if(auth()->user()->isGenAdmin){

            foreach (Store::all() as $store) {

                $summary = [
                    'name' => $store->name,
                    'balance' => $store->sales->whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->sum(fn($sale)=> $sale->payments->where('type','balance')->sum('amount')),
                    'cash' => $store->sales->whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->sum(fn($sale)=> $sale->payments->where('type','cash')->sum('amount')),
                    'expenses' => $store->expenses->whereBetween('updated_at', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])->sum(fn($q)=> $q->items->sum(fn($it)=> $it->price * $it->qty)),

                ];

                array_push($storesSales, $summary);
            }
        }

        $data = [
            'total_sales' => $total_cash + $total_balance,
            'total_sales_today' => $total_cash + $total_balance,
            'total_expenses_today' => $total_expenses,
            'total_expenses' => $total_expenses,
            'balance' =>  $total_balance,
            'stores_summaries' => $storesSales,
        ];

        return $data;
    }
}
