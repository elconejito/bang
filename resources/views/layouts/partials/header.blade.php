<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand" href="{{ url('/') }}">Bang</span>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#primary-navigation" aria-controls="primary-navigation" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="primary-navigation">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link  {{ active('home') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('calibers.index') }}" class="nav-link {{ active('calibers.index') }}">Calibers</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('firearms.index') }}" class="nav-link {{ active('firearms.index') }}">Firearms</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('targets.index') }}" class="nav-link {{ active('targets.index') }}">Targets</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('magazines.index') }}" class="nav-link {{ active('magazines.index') }}">Magazines</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ active('orders.index') }}">Orders</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('training.index') }}" class="nav-link {{ active('training.index') }}" >Training</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ranges.index') }}" class="nav-link {{ active('ranges.index') }}">Ranges</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('stores.index') }}" class="nav-link {{ active('stores.index') }}">Stores</a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link {{ active('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="nav-link {{ active('register') }}">Register</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <div  class="dropdown-menu" role="menu">
                            <a href="{{ route('logout') }}"
                               class="dropdown-item"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
