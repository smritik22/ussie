<div class="col-lg-3">
    <div class="fillter-mobile fillter-show d-lg-none">
        <h5>{{ $labels['filter'] }}</h5> <img src="{{ asset('assets/img/filtericon.svg') }}" alt="icon" />
    </div>
    <div class="filter-box-outer">
        <div class="filter-box-heading">
            <h5>{{ $labels['filter'] }}</h5>
            <a class="d-none d-lg-block" href=""
                onclick="event.preventDefault();clearfilters();">{{ $labels['clear_all'] }}</a>
            <a class="d-lg-none fillter-close" href="#"><img src="{{ asset('assets/img/close-black.svg') }}"
                    alt="icon"></a>
        </div>
        @if($is_search && $property_type)
            <input type="hidden" name="property_type[]" value="">
        @else
            <div class="filter-box">
                <div class="accordion" id="propertyTypeAccordion">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingType">
                            <button class="accordion-button p-0" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseType" aria-expanded="true" aria-controls="collapseType">
                                {{ $labels['property_type'] }}
                            </button>
                        </h2>
                        <div id="collapseType" class="accordion-collapse collapse show" aria-labelledby="headingType"
                            data-bs-parent="#propertyTypeAccordion">
                            <div class="accordion-body property-type-box p-0">
                                @foreach ($filters['property_types'] as $key => $value)
                                    <div class="form-check">
                                        <input class="form-check-input" name="property_type[]" class="propertyTypeClass"
                                            type="checkbox" value="{{ $value->id }}"
                                            id="propertyTypeId_{{ $value->id }}">
                                        <label class="form-check-label" for="propertyTypeId_{{ $value->id }}">
                                            {{ $language_id != 1 && @$value->childdata[0]->type ? $value->childdata[0]->type : $value->type }}
                                        </label>
                                    </div>
                                @endforeach
                                <a class="filter-box-view-all d-none" href="#">{{ $labels['view_all'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="filter-box">
            <div class="accordion" id="priceRangeaccordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingPrice">
                        <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapsePrice" aria-expanded="true" aria-controls="collapsePrice">
                            {{ $labels['price'] }}
                        </button>
                    </h2>
                    <div id="collapsePrice" class="accordion-collapse collapse " aria-labelledby="headingPrice"
                        data-bs-parent="#priceRangeaccordion">
                        <div class="accordion-body p-0">
                            <div class="price-text">
                                <input type="text" id="price" data-min="{{ $filters['min_price'] }}"
                                    data-max="{{ $filters['max_price'] }}" data-val_min="1"
                                    data-val_max="{{ $filters['max_price'] }}" data-curr="{{ $currency }}" data-steps="{{env('PRICE_RANGE_STEPS', 1000)}}">
                            </div>
                            <div id="slider-3" data-onstopcallback="sliderStopped"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-box">
            <div class="accordion" id="bedroomTypeaccordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingBedrooms">
                        <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseBedrooms" aria-expanded="true" aria-controls="collapseBedrooms">
                            {{ $labels['bedrooms'] }}
                        </button>
                    </h2>
                    <div id="collapseBedrooms" class="accordion-collapse collapse " aria-labelledby="headingBedrooms"
                        data-bs-parent="#bedroomTypeaccordion">
                        <div class="accordion-body p-0">
                            @foreach ($filters['bedroom_types'] as $key => $value)
                                <div class="form-check">
                                    <input class="form-check-input" name="bedroom_types[]" type="checkbox"
                                        value="{{ $value->id }}" id="bedroomTypeId_{{ $value->id }}">
                                    <label class="form-check-label" for="bedroomTypeId_{{ $value->id }}">
                                        {{ $language_id != 1 && @$value->childdata[0]->type ? @$value->childdata[0]->type : $value->type }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-box">
            <div class="accordion" id="bathroomTypeaccordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingBathrooms">
                        <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseBathrooms" aria-expanded="true" aria-controls="collapseBathrooms">
                            {{ $labels['bathrooms'] }}
                        </button>
                    </h2>
                    <div id="collapseBathrooms" class="accordion-collapse collapse" aria-labelledby="headingBathrooms"
                        data-bs-parent="#bathroomTypeaccordion">
                        <div class="accordion-body p-0">
                            @foreach ($filters['bathroom_types'] as $value)
                                <div class="form-check">
                                    <input class="form-check-input" class="bathroomTypeClass" type="checkbox"
                                        name="bathroom_types[]" value="{{ $value->id }}"
                                        id="PropertyBathroomsId_{{ $value->id }}">
                                    <label class="form-check-label" for="PropertyBathroomsId_{{ $value->id }}">
                                        {{ $language_id != 1 && @$value->childdata[0]->type ? $value->childdata[0]->type : $value->type }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-box">
            <div class="accordion" id="propertyAreasqftAccordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingAreasqft">
                        <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseArea" aria-expanded="true" aria-controls="collapseArea">
                            {{ $labels['area_sqft'] }}
                        </button>
                    </h2>
                    <div id="collapseArea" class="accordion-collapse collapse" aria-labelledby="headingAreasqft"
                        data-bs-parent="#propertyAreasqftAccordion">
                        <div class="accordion-body p-0">
                            @foreach ($areasqft_list as $key => $item)
                                <div class="form-check">
                                    <input class="form-check-input" name="area_sqft" type="radio" class="areasqftsClass"
                                        value="{{ $item['value'] }}" id="PropertyAreasqft_{{ $key }}">
                                    <label class="form-check-label" for="PropertyAreasqft_{{ $key }}">
                                        {{ $item['value'] }} {{ $labels['sqft'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-box">
            <div class="accordion" id="conditionAccordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingCondition ">
                        <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseCondition " aria-expanded="true"
                            aria-controls="collapseCondition ">
                            {{ $labels['condition'] }}
                        </button>
                    </h2>
                    <div id="collapseCondition" class="accordion-collapse collapse" aria-labelledby="headingCondition"
                        data-bs-parent="#conditionAccordion">
                        <div class="accordion-body p-0">
                            @foreach ($filters['condition_types'] as $key => $value)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition_type" value="{{$value->id}}"
                                        id="PropertyCondition{{ $key }}">
                                    <label class="form-check-label" for="PropertyCondition{{ $key }}">
                                        {{ $language_id != 1 && @$value->childdata[0]->condition_text? $value->childdata[0]->condition_text: $value->condition_text }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-box pb-0">
            <div class="accordion" id="completionStatusAccordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingStatus ">
                        <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseStatus " aria-expanded="true" aria-controls="collapseStatus ">
                            {{ $labels['completion_status'] }}
                        </button>
                    </h2>
                    <div id="collapseStatus" class="accordion-collapse collapse" aria-labelledby="headingStatus"
                        data-bs-parent="#completionStatusAccordion">
                        <div class="accordion-body p-0">
                            @foreach ($filters['completion_status'] as $key => $value)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="completion_status"
                                        value="{{ $value->id }}" id="completionStatusId{{ $key }}">
                                    <label class="form-check-label" for="completionStatusId{{ $key }}">
                                        {{ $language_id != 1 && @$value->childdata[0]->completion_type? $value->childdata[0]->completion_type: $value->completion_type }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mobile-btn-apply d-lg-none">
            <a href="#" onclick="event.preventDefault();clearfilters();">{{ $labels['clear_all'] }}</a>
            <a href="#" onclick="applyFilter(this,event);">{{ $labels['apply'] }}</a>
        </div>
    </div>
</div>
