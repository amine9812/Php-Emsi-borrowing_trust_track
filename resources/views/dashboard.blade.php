{{--
    Dashboard view: stats summary, leaderboards, and overdue list.
--}}
@extends('layout')

@section('title', 'Dashboard')

@section('content')
<section class="card">
    <h1>Dashboard</h1>
    <div class="grid grid-3">
        <div class="stat">
            <div class="label">Open loans</div>
            <div class="value">{{ $openCount }}</div>
        </div>
        <div class="stat">
            <div class="label">Overdue loans</div>
            <div class="value">{{ $overdueCount }}</div>
        </div>
        <div class="stat">
            <div class="label">Trust scale</div>
            <div class="value">0 - 100</div>
        </div>
    </div>
</section>

<section class="grid grid-2">
    <div class="card">
        <h2>Top trusted borrowers</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topBorrowers as $borrower)
                    <tr>
                        <td>
                            <a href="{{ route('borrowers.show', $borrower) }}">
                                {{ $borrower->name }}
                            </a>
                        </td>
                        <td>{{ $borrower->trust_score }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No borrowers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Lowest trusted borrowers</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bottomBorrowers as $borrower)
                    <tr>
                        <td>
                            <a href="{{ route('borrowers.show', $borrower) }}">
                                {{ $borrower->name }}
                            </a>
                        </td>
                        <td>{{ $borrower->trust_score }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No borrowers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>Overdue loans</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Borrower</th>
                <th>Item</th>
                <th>Due date</th>
                <th>Days late</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($overdueLoans as $loan)
                @php $daysLate = max(0, date_diff_days($today, $loan->due_date)); @endphp
                <tr>
                    <td>{{ $loan->borrower->name }}</td>
                    <td>{{ $loan->item->name }}</td>
                    <td>{{ $loan->due_date }}</td>
                    <td><span class="badge danger">{{ $daysLate }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4">No overdue loans. Great job!</td></tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
