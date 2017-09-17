<div class="picture-main">
    @if($pictures->isEmpty())
        <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid img-thumbnail" alt="No Picture Yet">
    @else
        <img src="{{ asset($pictures->first()->getPath('medium')) }}" class="img-fluid img-thumbnail" alt="{{ $pictures->first()->name }}">
    @endif
</div>
<div class="pictures-thumbnails row">
    @foreach($pictures as $picture)
        <div class="thumbnail col-6 col-lg-4">
            <img src="{{ asset($picture->getPath()) }}" class="img-thumbnail" alt="{{ $picture->name }}">
        </div>
    @endforeach
</div>
