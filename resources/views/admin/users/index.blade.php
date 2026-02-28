@extends('admin.layout')

@section('title', 'Users Management')

@section('content')
<!-- Customers Management Card -->
<div class="card">
    <div class="card-header">
        <div>
            <h3><i class="fas fa-users"></i> Customers Management</h3>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Manage customer accounts and activity</p>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-user"></i> Customer</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th style="text-align: center;"><i class="fas fa-shopping-cart"></i> Orders</th>
                    <th style="text-align: right;"><i class="fas fa-dollar-sign"></i> Total Spent</th>
                    <th style="text-align: center;"><i class="fas fa-calendar-alt"></i> Joined</th>
                    <th style="text-align: center;"><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="transition: all 0.2s ease;">
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6 to #06b6d4); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong style="display: block;">{{ $user->name }}</strong>
                                <span style="font-size: 11px; color: #94a3b8;">ID: {{ $user->id }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="mailto:{{ $user->email }}" style="color: #3b82f6; hover: text-decoration: underline;">{{ $user->email }}</a>
                    </td>
                    <td style="text-align: center;">
                        @php $orderCount = $user->orders()->count(); @endphp
                        <span style="background: rgba(59, 130, 246, 0.2); padding: 6px 10px; border-radius: 6px; color: #3b82f6; font-weight: 600;">
                            {{ $orderCount }} order{{ $orderCount !== 1 ? 's' : '' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <strong style="color: #10b981; font-size: 16px;">${{ number_format($user->orders()->sum('total'), 2) }}</strong>
                    </td>
                    <td style="text-align: center;">
                        <div style="font-size: 13px;">{{ $user->created_at->format('M d, Y') }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $user->created_at->diffForHumans() }}</div>
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 6px; justify-content: center;">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-eye"></i> View</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">
                        <div class="empty-state">
                            <div style="font-size: 48px; margin-bottom: 15px;"><i class="fas fa-users"></i></div>
                            <div style="font-size: 16px; color: #cbd5e1;">No customers found</div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Customer accounts will appear here</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="padding: 20px; border-top: 1px solid rgba(148, 163, 184, 0.1); text-align: center;">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
