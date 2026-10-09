@php $product = $product ?? null; $editing = $product !== null; @endphp
<form action="{{ $editing ? route('products.update', $product) : route('products.store') }}" method="post" class="card">
    @csrf
    @if ($editing) @method('PUT') @endif
    <div class="card-header"><div><h2 class="card-title">{{ $editing ? 'Informasi Produk' : 'Informasi Produk Baru' }}</h2><p class="mt-1 text-xs text-muted">Isi data produk dengan benar.</p></div></div>
    <div class="grid gap-5 p-5 sm:grid-cols-2">
        <div><label class="label" for="code">Kode Produk</label><input class="input @error('code') input-error @enderror" id="code" name="code" value="{{ old('code', $product?->code) }}" maxlength="20" required>@error('code') <p class="field-error">{{ $message }}</p> @enderror</div>
        <div><label class="label" for="name">Nama Produk</label><input class="input @error('name') input-error @enderror" id="name" name="name" value="{{ old('name', $product?->name) }}" required>@error('name') <p class="field-error">{{ $message }}</p> @enderror</div>
        <div><label class="label" for="purchase_price">Harga Beli</label><input class="input @error('purchase_price') input-error @enderror" id="purchase_price" name="purchase_price" type="number" min="0" step="1" value="{{ old('purchase_price', $product?->purchase_price ?? 0) }}" required>@error('purchase_price') <p class="field-error">{{ $message }}</p> @enderror</div>
        <div><label class="label" for="selling_price">Harga Jual</label><input class="input @error('selling_price') input-error @enderror" id="selling_price" name="selling_price" type="number" min="0" step="1" value="{{ old('selling_price', $product?->selling_price) }}" required>@error('selling_price') <p class="field-error">{{ $message }}</p> @enderror</div>
        <div><label class="label" for="stock">Stok</label><input class="input @error('stock') input-error @enderror" id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product?->stock ?? 0) }}" required>@error('stock') <p class="field-error">{{ $message }}</p> @enderror</div>
        <div><label class="label" for="unit">Satuan</label><select class="select @error('unit') input-error @enderror" id="unit" name="unit" required>@foreach ($satuan as $unit)<option value="{{ $unit }}" @selected(old('unit', $product?->unit ?? 'pcs') === $unit)>{{ ucfirst($unit) }}</option>@endforeach</select>@error('unit') <p class="field-error">{{ $message }}</p> @enderror</div>
    </div>
    <div class="flex flex-wrap justify-end gap-3 border-t border-line px-5 py-4"><a class="btn-secondary" href="{{ route('products.index') }}">Batal</a><button class="btn-primary" type="submit">{{ $editing ? 'Update Produk' : 'Simpan Produk' }}</button></div>
</form>