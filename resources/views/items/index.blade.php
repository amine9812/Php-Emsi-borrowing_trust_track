{{--
    Items list with add/edit form.
--}}
@extends('layout')

@section('title', 'Items')

@section('content')
@php
    $editing = $editItem !== null;
@endphp

<section class="card">
    <h1>Items</h1>
    <p class="helper">Manage items that can be loaned out to borrowers.</p>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Serial</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                @php
                    $isAvailable = $item->is_active && $item->open_loans_count === 0;
                @endphp
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->serial }}</td>
                    <td>
                        @if (!$item->is_active)
                            <span class="badge">Inactive</span>
                        @else
                            <span class="badge {{ $isAvailable ? 'ok' : 'warn' }}">
                                {{ $isAvailable ? 'Available' : 'On loan' }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="inline-actions">
                            <a class="button secondary" href="{{ route('items.index', ['edit' => $item->id]) }}">Edit</a>
                            <form method="post" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button class="button danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No items yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>

<section class="card">
    <h2>{{ $editing ? 'Edit Item' : 'Add Item' }}</h2>
    @if ($errors->any())
        <div class="flash flash-error">
            {{ implode(' ', $errors->all()) }}
        </div>
    @endif
    <form method="post" action="{{ $editing ? route('items.update', $editItem) : route('items.store') }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif
        <div class="form-row grid-2">
            <div>
                <label for="name">Name *</label>
                <input id="name" name="name" value="{{ old('name', $editItem->name ?? '') }}" required>
            </div>
            <div>
                <label for="category">Category</label>
                <input id="category" name="category" value="{{ old('category', $editItem->category ?? '') }}">
            </div>
        </div>
        <div class="form-row grid-2">
            <div>
                <label for="serial">Serial</label>
                <input id="serial" name="serial" value="{{ old('serial', $editItem->serial ?? '') }}">
            </div>
            <div>
                <label for="is_active">
                    <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $editItem->is_active ?? 1) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes">{{ old('notes', $editItem->notes ?? '') }}</textarea>
            </div>
        </div>
        <button class="button" type="submit">Save item</button>
        @if ($editing)
            <a class="button secondary" href="{{ route('items.index') }}">Cancel</a>
        @endif
    </form>
</section>
@endsection
