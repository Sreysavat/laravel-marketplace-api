{{-- resources/views/auth/register.blade.php --}}

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<div class="container mt-5">

    <h1 class="mb-4">Register</h1>

    <form action="{{ route('register') }}" method="post">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control", placeholder="Enter Full name">
            <span><div class="text-danger">@error('name') {{ $message }} @enderror</div></span>
                
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter Email">
            <span><div class="text-danger">@error('email') {{ $message }} @enderror</div></span>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter Password">
            <span><div class="text-danger">@error('password') {{ $message }} @enderror</div></span>
        </div>
         <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
            <span><div class="text-danger">@error('password_confirmation') {{ $message }} @enderror</div></span>
        </div>

        <button type="submit" class="btn btn-primary">
            Register
        </button>
        <br>
        <a href="login">have an account</a>
    </form>

</div>
