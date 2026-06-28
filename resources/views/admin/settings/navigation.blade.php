@extends('layouts.admin')
@section('title', 'Navigation Menu')
@section('content')
<div class="card">
    <div class="card-header"><h3>📋 Navigation Menu</h3></div>
    <div class="card-body">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        
        <form method="POST" action="{{ route('admin.settings.navigation.store') }}" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="Menu Title" required></div>
                <div class="col-md-3"><input type="text" name="url" class="form-control" placeholder="URL" required></div>
                <div class="col-md-2">
                    <select name="location" class="form-control">
                        <option value="header">Header</option>
                        <option value="footer">Footer</option>
                        <option value="sidebar">Sidebar</option>
                    </select>
                </div>
                <div class="col-md-2"><input type="text" name="icon" class="form-control" placeholder="Icon (optional)"></div>
                <div class="col-md-2"><button class="btn btn-primary">Add Menu</button></div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr><th>Title</th><th>URL</th><th>Location</th><th>Icon</th><th>Order</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($menus as $menu)
                <tr>
                    <td>{{ $menu->title }}</td>
                    <td>{{ $menu->url }}</td>
                    <td>{{ $menu->location }}</td>
                    <td>{{ $menu->icon ?? '-' }}</td>
                    <td>{{ $menu->order }}</td>
                    <td>{{ $menu->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.settings.navigation.delete', $menu) }}" style="display:inline" onsubmit="return confirm('Delete this menu item?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No menu items found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection