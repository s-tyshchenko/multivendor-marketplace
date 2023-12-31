<li
    class="dropdown-swatches-wrapper"
    data-type="dropdown"
>
    <div
        class="attribute-name"
        data-title="{{ $set->title }}"
    >{{ $set->title }}</div>
    <div class="attribute-values">
        <div class="dropdown-swatch">
            <label>
                <select
                    class="form-select product-filter-item"
                    name="attributes[{{ $set->slug }}][]"
                    multiple
                >
                    <option value="">{{ __('-- Select --') }}</option>
                    @foreach ($attributes->where('attribute_set_id', $set->id) as $attribute)
                        <option
                            value="{{ $attribute->id }}"
                            @selected(in_array($attribute->id, $selected))
                        >{{ $attribute->title }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>
</li>
