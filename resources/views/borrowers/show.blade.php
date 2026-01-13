{{--
    Borrower detail view: trust score, loan history, and trust events.
--}}
@extends('layout')

@section('title', 'Borrower Details')

@section('content')
<section class="card">
    <h1>{{ $borrower->name }}</h1>
    <p class="helper">Current trust score</p>
    <div class="stat">
        <div class="value">{{ $borrower->trust_score }}</div>
    </div>
    <p>
        {{ $borrower->email }}
        @if ($borrower->phone)
            - {{ $borrower->phone }}
        @endif
    </p>
</section>

<section class="card">
    <h2>Loan history</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Loan date</th>
                <th>Due date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $loan)
                <tr>
                    <td>{{ $loan->item->name }}</td>
                    <td>{{ $loan->loan_date }}</td>
                    <td>{{ $loan->due_date }}</td>
                    <td>
                        <span class="badge {{ $loan->status === 'open' ? 'warn' : ($loan->status === 'lost' ? 'danger' : 'ok') }}">
                            {{ $loan->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No loans recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>

<section class="card">
    <h2>Trust events</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reason</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $event)
                <tr>
                    <td>{{ $event->created_at }}</td>
                    <td>{{ $event->reason }}</td>
                    <td>{{ $event->points_delta }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No trust events yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
