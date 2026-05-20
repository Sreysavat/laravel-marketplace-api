{{-- resources/views/dashboard.blade.php --}}


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dashboard</h1>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="btn btn-danger">
                Logout
            </button>
        </form>
    </div>

    <div class="card p-4 shadow-sm">
        <h3>Welcome</h3>
        <p>This is your dashboard page.</p>
    </div>

</div>
