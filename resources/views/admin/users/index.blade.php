@extends('layouts.admin')
@section('title', 'Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Customers</h3>
</div>

<form method="GET" class="d-flex gap-2 mb-4">
    <input type="text" name="search" class="form-control form-control-sm w-auto" placeholder="Search by name, email or phone…" value="{{ request('search') }}">
    <button type="submit" class="btn btn-sm btn-outline-success">Search</button>
    @if(request('search'))
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
    @endif
</form>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email ?? '—' }}</td>
                        <td>{{ $user->phone ?? '—' }}</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Deactivated</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <form method="POST" action="{{ route('admin.users.deactivate', $user) }}"
                                      onsubmit="return confirm('Deactivate {{ addslashes($user->name) }}? This will cancel their pending orders.')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Deactivate</button>
                                </form>
                            @else
                                <span class="text-muted small">Deactivated</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            @if(request('search'))
                                No customers found matching "{{ request('search') }}".
                            @else
                                No customers yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->withQueryString()->links() }}</div>
@endsection
