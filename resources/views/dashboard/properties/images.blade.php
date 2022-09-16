<div class="row">
    @if ($property->propertyImages->count() > 0)
        @foreach ($property->propertyImages as $key => $image)
            <div class="col-sm-3" style="margin-bottom: 15px">
                <a href="{{ URL::asset('storage/property_images/' . $property->id . '/' . $image->property_image) }}" target="_blank">
                    <img class="" height="140px" width="200px"
                        src="{{ URL::asset('storage/property_images/' . $property->id . '/' . $image->property_image) }}"
                        alt="{{ $image->property_image ?: 'Property Image' }}">
                </a>
            </div>
        @endforeach
    @else
        <div class="col-sm-12">
            <h4>No Records Found...</h4>
        </div>
    @endif
</div>
