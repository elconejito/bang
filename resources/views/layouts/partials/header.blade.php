<nav class="navbar navbar-toggleable-sm navbar-static-top navbar-inverse bg-inverse">
    <div class="container">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span class="navbar-brand" href="{{ url('/') }}">Bang</span>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
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
    </div>
</nav>