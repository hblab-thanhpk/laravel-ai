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
            <label for="name">Tên danh mục</label>
            <input id="name" name="name" type="text" value="{{ old('name', isset($category) ? $category->name : '') }}" required>
        </div>

        <div class="field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', isset($category) ? $category->slug : '') }}" required>
            <span class="help-text">Ví dụ: <span class="inline-code">thoi-trang-nam</span></span>
        </div>
    </div>

    <div class="field" style="margin-top: 1rem;">
        <label for="parent_id">Danh mục cha</label>
        <select id="parent_id" name="parent_id">
            <option value="">— Không có (danh mục gốc) —</option>
            @foreach ($parentOptions as $option)
                <option
                    value="{{ $option->id }}"
                    @selected(old('parent_id', isset($category) ? $category->parent_id : '') === $option->id)
                >
                    {{ str_repeat('　', $option->depth) }}{{ $option->depth > 0 ? '└ ' : '' }}{{ $option->name }}
                </option>
            @endforeach
        </select>
        <span class="help-text">Chọn vị trí trong cây danh mục.</span>
    </div>

    <div class="field" style="margin-top: 1rem;">
        <label for="description">Mô tả</label>
        <textarea id="description" name="description">{{ old('description', isset($category) ? $category->description : '') }}</textarea>
    </div>

    <label class="field-checkbox">
        <input type="hidden" name="is_active" value="0">
        <input id="is_active" name="is_active" type="checkbox" value="1" @checked((bool) old('is_active', isset($category) ? $category->is_active : true))>
        <span>Danh mục đang hoạt động</span>
    </label>

    <div class="form-actions">
        <button class="button" type="submit">{{ $submitLabel }}</button>
        <a class="button-secondary" href="{{ isset($category) ? route('admin.categories.show', $category) : route('admin.categories.index') }}">Hủy</a>
    </div>
</div>
