<div class="row">
    <div class="col page-header">
        {!! Breadcrumbs::render($breadcrumbName, $breadcrumbParams) !!}
        @if( $hasButton )
        <a href="{{ $buttonLink }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> {{ $buttonText }}</a>
        @endif
        <h1>{!! $pageTitle !!}</h1>
    </div>
</div>