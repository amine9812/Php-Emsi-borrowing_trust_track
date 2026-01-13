<?php
// Loan controller: handles loan creation, listing, and return processing.

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Models\Item;
use App\Models\Loan;
use App\Services\TrustService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'open');
        $statusFilter = $status === 'all' ? null : $status;

        $query = Loan::with(['borrower', 'item'])
            ->orderByDesc('loan_date')
            ->orderByDesc('id');

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $loans = $query->get();
        $borrowers = Borrower::orderBy('name')->get();
        $availableItems = Item::where('is_active', 1)
            ->whereDoesntHave('openLoans')
            ->orderBy('name')
            ->get();

        $returnLoan = null;
        if ($request->query('return')) {
            $returnLoan = Loan::with(['borrower', 'item'])->find((int) $request->query('return'));
            if ($returnLoan && $returnLoan->status !== 'open') {
                $request->session()->flash('error', 'This loan is already closed.');
                $returnLoan = null;
            }
        }

        return view('loans.index', [
            'loans' => $loans,
            'borrowers' => $borrowers,
            'availableItems' => $availableItems,
            'returnLoan' => $returnLoan,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'borrower_id' => 'required|integer|exists:borrowers,id',
            'item_id' => 'required|integer|exists:items,id',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $itemAvailable = Item::where('id', $data['item_id'])
            ->where('is_active', 1)
            ->whereDoesntHave('openLoans')
            ->exists();

        if (!$itemAvailable) {
            return redirect()->route('loans.index')
                ->withErrors(['item_id' => 'Item is currently on an open loan.'])
                ->withInput();
        }

        Loan::create([
            'borrower_id' => $data['borrower_id'],
            'item_id' => $data['item_id'],
            'loan_date' => today_date(),
            'due_date' => $data['due_date'],
            'status' => 'open',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('loans.index')->with('success', 'Loan created.');
    }

    public function processReturn(Request $request, Loan $loan, TrustService $trustService)
    {
        $validator = Validator::make($request->all(), [
            'returned_at' => 'nullable|date',
            'return_condition' => 'required|in:ok,damaged_minor,damaged_major,lost',
            'notes' => 'nullable|string',
        ], [
            'return_condition.required' => 'Return condition is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('loans.index', ['return' => $loan->id])
                ->withErrors($validator, 'return');
        }

        $returnedAt = $request->input('returned_at') ?: today_date();
        $condition = $request->input('return_condition');
        $notes = (string) ($request->input('notes') ?? '');

        $result = $trustService->processReturn($loan, $returnedAt, $condition, $notes);
        if (!$result['ok']) {
            return redirect()->route('loans.index', ['return' => $loan->id])
                ->withErrors(['return' => $result['error']], 'return');
        }

        return redirect()->route('loans.index')->with('success', 'Loan updated and trust score adjusted.');
    }
}
