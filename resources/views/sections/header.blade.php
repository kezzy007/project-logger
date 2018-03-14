<!--/.Navbar danger color-->                                
<nav class="navbar  fixed-top navbar-dark indigo darken-3">
<a class="navbar-brand" href="dashboard">Logging system</a>

    @if(auth()->check())
    <form action="{{ url('logout') }}" method="POST">
        
        {{ csrf_field() }}

        <button type="submit" class="btn btn-default">Logout</button>
    </form>
    @endif

</nav>
<!--/.Navbar danger color-->