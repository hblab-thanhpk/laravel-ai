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
            <label for="category_id">Danh mục</label>
            <select id="category_id" name="category_id">
                <option value="">-- Chưa phân loại --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', isset($product) ? $product->category_id : '') === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="name">Tên sản phẩm</label>
            <input id="name" name="name" type="text" value="{{ old('name', isset($product) ? $product->name : '') }}" required>
        </div>

        <div class="field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', isset($product) ? $product->slug : '') }}" required>
        </div>

        <div class="field">
            <label for="sku">SKU</label>
            <input id="sku" name="sku" type="text" value="{{ old('sku', isset($product) ? $product->sku : '') }}" required>
        </div>

        <div class="field">
            <label for="price">Giá</label>
            <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', isset($product) ? $product->price : '0') }}" required>
        </div>

        <div class="field">
            <label for="stock">Tồn kho</label>
            <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', isset($product) ? $product->stock : '0') }}" required>
        </div>
    </div>

    <div class="field" style="margin-top: 1rem;">
        <label for="description">Mô tả</label>
        <textarea id="description" name="description">{{ old('description', isset($product) ? $product->description : '') }}</textarea>
    </div>

    <label class="field-checkbox">
        <input type="hidden" name="is_active" value="0">
        <input id="is_active" name="is_active" type="checkbox" value="1" @checked((bool) old('is_active', isset($product) ? $product->is_active : true))>
        <span>Sản phẩm đang hoạt động</span>
    </label>

    <div class="form-actions">
        <button class="button" type="submit">{{ $submitLabel }}</button>
        <a class="button-secondary" href="{{ isset($product) ? route('admin.products.show', $product) : route('admin.products.index') }}">Hủy</a>
    </div>
</div>
