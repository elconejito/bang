<nav class="navbar navbar-static-top navbar-dark bg-inverse">
    <div class="container">
        <a class="navbar-brand" href="#">Bang</a>
        <ul class="nav navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}/">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('firearms.index') }}">Firearms</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('cartridges.index') }}">Cartridges</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('bullets.index') }}">Bullets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('orders.index') }}">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('shoots.index') }}">Shoots</a>
            </li>
        </ul>
    </div>
</nav>