{{--
    Loans list with create form and return workflow.
--}}
@extends('layout')

@section('title', 'Loans')

@section('content')
<section class="card">
    <h1>Loans</h1>
    <p class="helper">Create loans, manage returns, and keep trust scores fair and transparent.</p>
</section>

<section class="card">
    <h2>New loan</h2>
    @if ($errors->any())
        <div class="flash flash-error">
            {{ implode(' ', $errors->all()) }}
        </div>
    @endif
    <form method="post" action="{{ route('loans.store') }}">
        @csrf
        <div class="form-row grid-2">
            <div>
                <label for="borrower_id">Borrower *</label>
                <select id="borrower_id" name="borrower_id" required>
                    <option value="">Select borrower</option>
                    @foreach ($borrowers as $borrower)
                        <option value="{{ $borrower->id }}" {{ (string) old('borrower_id') === (string) $borrower->id ? 'selected' : '' }}>
                            {{ $borrower->name }} ({{ $borrower->trust_score }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="item_id">Item *</label>
                <select id="item_id" name="item_id" required>
                    <option value="">Select available item</option>
                    @foreach ($availableItems as $item)
                        <option value="{{ $item->id }}" {{ (string) old('item_id') === (string) $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row grid-2">
            <div>
                <label for="due_date">Due date *</label>
                <input id="due_date" name="due_date" type="date" value="{{ old('due_date') }}" required>
            </div>
            <div>
                <label for="notes">Notes</label>
                <input id="notes" name="notes" value="{{ old('notes') }}" placeholder="Optional notes">
            </div>
        </div>
        <button class="button" type="submit">Create loan</button>
    </form>
</section>

@if ($returnLoan)
<section class="card">
    <h2>Return loan</h2>
    <p class="helper">Loan: {{ $returnLoan->borrower->name }} - {{ $returnLoan->item->name }}</p>
    @php
        $returnErrors = $errors->getBag('return')->all();
    @endphp
    @if (!empty($returnErrors))
        <div class="flash flash-error">
            {{ implode(' ', $returnErrors) }}
        </div>
    @endif
    <form method="post" action="{{ route('loans.return', $returnLoan) }}">
        @csrf
        <div class="form-row grid-2">
            <div>
                <label for="returned_at">Returned date</label>
                <input id="returned_at" name="returned_at" type="date" value="{{ today_date() }}">
            </div>
            <div>
                <label for="return_condition">Condition *</label>
                <select id="return_condition" name="return_condition" required>
                    <option value="">Select condition</option>
                    <option value="ok">OK</option>
                    <option value="damaged_minor">Damaged (minor)</option>
                    <option value="damaged_major">Damaged (major)</option>
                    <option value="lost">Lost</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label for="return_notes">Notes</label>
                <textarea id="return_notes" name="notes"></textarea>
            </div>
        </div>
        <button class="button" type="submit">Finalize return</button>
        <a class="button secondary" href="{{ route('loans.index') }}">Cancel</a>
    </form>
</section>
@endif

<section class="card">
    <h2>Loan list</h2>
    <div class="inline-actions" style="margin-bottom: 1rem;">
        <a class="button secondary" href="{{ route('loans.index', ['status' => 'open']) }}">Open</a>
        <a class="button secondary" href="{{ route('loans.index', ['status' => 'returned']) }}">Returned</a>
        <a class="button secondary" href="{{ route('loans.index', ['status' => 'lost']) }}">Lost</a>
        <a class="button secondary" href="{{ route('loans.index', ['status' => 'all']) }}">All</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Borrower</th>
                <th>Item</th>
                <th>Loan date</th>
                <th>Due date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $loan)
                @php
                    $statusClass = $loan->status === 'open' ? 'warn' : ($loan->status === 'lost' ? 'danger' : 'ok');
                    $daysLate = $loan->status === 'open' ? max(0, date_diff_days(today_date(), $loan->due_date)) : 0;
                @endphp
                <tr>
                    <td>{{ $loan->borrower->name }}</td>
                    <td>{{ $loan->item->name }}</td>
                    <td>{{ $loan->loan_date }}</td>
                    <td>
                        {{ $loan->due_date }}
                        @if ($daysLate > 0)
                            <span class="badge danger">{{ $daysLate }} late</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $statusClass }}">{{ $loan->status }}</span></td>
                    <td>
                        @if ($loan->status === 'open')
                            <a class="button secondary" href="{{ route('loans.index', ['return' => $loan->id]) }}">Return</a>
                        @else
                            <span class="helper">Closed</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No loans match this filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
