<div class="card">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-grid">
        <div class="field">
            <label for="sku">SKU biến thể</label>
            <input id="sku" name="sku" type="text" value="{{ old('sku', isset($variant) ? $variant->sku : '') }}" required>
        </div>

        <div class="field">
            <label for="size">Size</label>
            <input id="size" name="size" type="text" value="{{ old('size', isset($variant) ? $variant->size : '') }}" placeholder="Ví dụ: S, M, L">
        </div>

        <div class="field">
            <label for="color">Color</label>
            <input id="color" name="color" type="text" value="{{ old('color', isset($variant) ? $variant->color : '') }}" placeholder="Ví dụ: Red, Black">
        </div>

        <div class="field">
            <label for="price">Giá biến thể (tùy chọn)</label>
            <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', isset($variant) ? $variant->price : '') }}">
            <span class="help-text">Để trống để dùng giá mặc định của product.</span>
        </div>

        <div class="field">
            <label for="stock">Tồn kho</label>
            <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', isset($variant) ? $variant->stock : '0') }}" required>
        </div>
    </div>

    <label class="field-checkbox">
        <input type="hidden" name="is_active" value="0">
        <input id="is_active" name="is_active" type="checkbox" value="1" @checked((bool) old('is_active', isset($variant) ? $variant->is_active : true))>
        <span>Variant đang hoạt động</span>
    </label>

    <div class="form-actions">
        <button class="button" type="submit">{{ $submitLabel }}</button>
        <a class="button-secondary" href="{{ route('admin.products.variants.index', $product) }}">Hủy</a>
    </div>
</div>
