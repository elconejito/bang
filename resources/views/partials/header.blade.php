<nav class="navbar navbar-static-top navbar-dark bg-inverse">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Bang</a>
        <ul class="nav navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('cartridges.index') }}">Cartridges</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('ranges.index') }}">Ranges</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('stores.index') }}">Stores</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('firearms.index') }}">Firearms</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('orders.index') }}">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('trips.index') }}">Range Trips</a>
            </li>
        </ul>
    </div>
</nav>