{{--
    Borrowers list with add/edit form.
--}}
@extends('layout')

@section('title', 'Borrowers')

@section('content')
@php
    $editing = $editBorrower !== null;
@endphp

<section class="card">
    <h1>Borrowers</h1>
    <p class="helper">Track who is borrowing items and how their trust score changes over time.</p>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Trust score</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($borrowers as $borrower)
                <tr>
                    <td>
                        <a href="{{ route('borrowers.show', $borrower) }}">
                            {{ $borrower->name }}
                        </a>
                    </td>
                    <td>
                        <div>{{ $borrower->email }}</div>
                        <div class="helper">{{ $borrower->phone }}</div>
                    </td>
                    <td>{{ $borrower->trust_score }}</td>
                    <td>
                        <div class="inline-actions">
                            <a class="button secondary" href="{{ route('borrowers.index', ['edit' => $borrower->id]) }}">Edit</a>
                            <form method="post" action="{{ route('borrowers.destroy', $borrower) }}" onsubmit="return confirm('Delete this borrower?');">
                                @csrf
                                @method('DELETE')
                                <button class="button danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No borrowers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>

<section class="card">
    <h2>{{ $editing ? 'Edit Borrower' : 'Add Borrower' }}</h2>
    @if ($errors->any())
        <div class="flash flash-error">
            {{ implode(' ', $errors->all()) }}
        </div>
    @endif
    <form method="post" action="{{ $editing ? route('borrowers.update', $editBorrower) : route('borrowers.store') }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif
        <div class="form-row grid-2">
            <div>
                <label for="name">Name *</label>
                <input id="name" name="name" value="{{ old('name', $editBorrower->name ?? '') }}" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" value="{{ old('email', $editBorrower->email ?? '') }}">
            </div>
        </div>
        <div class="form-row grid-2">
            <div>
                <label for="phone">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone', $editBorrower->phone ?? '') }}">
            </div>
        </div>
        <button class="button" type="submit">Save borrower</button>
        @if ($editing)
            <a class="button secondary" href="{{ route('borrowers.index') }}">Cancel</a>
        @endif
    </form>
</section>
@endsection
