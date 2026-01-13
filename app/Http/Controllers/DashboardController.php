<?php
// Dashboard controller: aggregates loan stats and trust leaderboards.

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page');
        if ($page) {
            if ($page === 'borrower' && $request->query('id')) {
                return redirect()->route('borrowers.show', ['borrower' => (int) $request->query('id')]);
            }
            $map = [
                'dashboard' => route('dashboard'),
                'borrowers' => route('borrowers.index'),
                'items' => route('items.index'),
                'loans' => route('loans.index'),
            ];
            return redirect($map[$page] ?? route('dashboard'));
        }

        $today = today_date();
        $openCount = Loan::where('status', 'open')->count();
        $overdueCount = Loan::where('status', 'open')->where('due_date', '<', $today)->count();
        $topBorrowers = Borrower::orderByDesc('trust_score')->orderBy('name')->limit(5)->get();
        $bottomBorrowers = Borrower::orderBy('trust_score')->orderBy('name')->limit(5)->get();
        $overdueLoans = Loan::with(['borrower', 'item'])
            ->where('status', 'open')
            ->where('due_date', '<', $today)
            ->orderBy('due_date')
            ->get();

        return view('dashboard', [
            'openCount' => $openCount,
            'overdueCount' => $overdueCount,
            'topBorrowers' => $topBorrowers,
            'bottomBorrowers' => $bottomBorrowers,
            'overdueLoans' => $overdueLoans,
            'today' => $today,
        ]);
    }
}
