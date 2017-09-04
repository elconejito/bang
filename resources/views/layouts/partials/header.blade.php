<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand" href="{{ url('/') }}">Bang</span>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#primary-navigation" aria-controls="primary-navigation" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="primary-navigation">
            <ul class="navbar-nav">
                <li><a class="nav-link" href="{{ url('/') }}">Dashboard</a></li>
                <li class="nav-item">
                    <a class="nav-link {{ active('cartridges.index') }}" href="{{ route('cartridges.index') }}">Cartridges</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active('ranges.index') }}" href="{{ route('ranges.index') }}">Ranges</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active('stores.index') }}" href="{{ route('stores.index') }}">Stores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active('firearms.index') }}" href="{{ route('firearms.index') }}">Firearms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active('orders.index') }}" href="{{ route('orders.index') }}">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active('trips.index') }}" href="{{ route('trips.index') }}">Range Trips</a>
                </li>
            </ul>
        </div>
    </div>
</nav>