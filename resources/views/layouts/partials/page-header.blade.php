<div class="row">
    <div class="col page-header">
        {!! Breadcrumbs::render($breadcrumbName, $breadcrumbParams) !!}
        @if( $hasButton )
        <a href="{{ $buttonLink }}" class="btn btn-outline-success float-sm-right">
            @isset($buttonTextIcon)
            <i class="{{ $buttonTextIcon }}"></i>
            @endif
            {{ $buttonText }}
        </a>
        @endif
        <h1>{!! $pageTitle !!}</h1>
    </div>
</div>
