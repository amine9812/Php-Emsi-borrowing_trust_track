<?php
// Borrower controller: CRUD operations and borrower detail pages.

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Models\Loan;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
    public function index(Request $request)
    {
        $borrowers = Borrower::orderBy('name')->get();
        $editBorrower = null;

        if ($request->query('edit')) {
            $editBorrower = Borrower::find((int) $request->query('edit'));
        }

        return view('borrowers.index', [
            'borrowers' => $borrowers,
            'editBorrower' => $editBorrower,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        Borrower::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'trust_score' => 100,
            'created_at' => now_iso(),
        ]);

        return redirect()->route('borrowers.index')->with('success', 'Borrower added.');
    }

    public function update(Request $request, Borrower $borrower)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        $borrower->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        return redirect()->route('borrowers.index')->with('success', 'Borrower updated.');
    }

    public function destroy(Borrower $borrower)
    {
        $hasLoans = Loan::where('borrower_id', $borrower->id)->exists();
        if ($hasLoans) {
            return redirect()->route('borrowers.index')->with('error', 'Cannot delete borrower with existing loans.');
        }

        $borrower->delete();
        return redirect()->route('borrowers.index')->with('success', 'Borrower deleted.');
    }

    public function show(Borrower $borrower)
    {
        $loans = $borrower->loans()->with('item')->orderByDesc('loan_date')->orderByDesc('id')->get();
        $events = $borrower->trustEvents()->orderByDesc('created_at')->orderByDesc('id')->get();

        return view('borrowers.show', [
            'borrower' => $borrower,
            'loans' => $loans,
            'events' => $events,
        ]);
    }
}
