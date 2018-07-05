<div class="row">
    <div class="col page-header">
        {!! Breadcrumbs::render($breadcrumbName, $breadcrumbParams) !!}
        @if( $hasButton )
        <a href="{{ $buttonLink }}" class="btn btn-success-outline pull-right"><i class="fas fa-plus-circle"></i> {{ $buttonText }}</a>
        @endif
        <h1>{!! $pageTitle !!}</h1>
    </div>
</div>
