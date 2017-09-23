{!! Breadcrumbs::render($breadcrumbName) !!}
@if( $hasButton )
<a href="{{ route($buttonRoute) }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> {{ $buttonText }}</a>
@endif
<h1>{{ $pageTitle }}</h1>