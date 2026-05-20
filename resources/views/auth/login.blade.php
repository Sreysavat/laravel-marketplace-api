{{-- resources/views/auth/login.blade.php --}}

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<div class="container mt-5">

    <h1 class="mb-4">Login</h1>

    <form action="{{ route('login') }}" method="POST">
    @if(Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
     @endif

     @if(Session::has('fail'))
        <div class="alert alert-danger">
            {{ Session::get('fail') }}
        </div>
        
    @endif
        @csrf


        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter password">
        </div>

        <button type="submit" class="btn btn-primary">
            Login
        </button>
        <br>
        <a href="register">don't have an account?</a>
    </form>

</div>
