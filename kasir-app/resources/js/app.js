import Swal from 'sweetalert2';

/**
 * Terjemahan untuk pesan yang dibangun di sisi klien. Disuntikkan oleh layout
 * (window.KoperasiKu) dan dipakai sebagai fallback berbahasa Indonesia.
 */
const appI18n = {
	confirm: 'Konfirmasi',
	yesContinue: 'Ya, lanjutkan',
	cancel: 'Batal',
	showPassword: 'Tampilkan password',
	hidePassword: 'Sembunyikan password',
	increase: 'Tambah :name',
	decrease: 'Kurangi :name',
	remove: 'Hapus',
	stockMax: 'Jumlah produk sudah mencapai stok maksimum.',
	scanError: 'Barcode tidak dapat diproses.',
	scanFailed: 'Gagal menghubungi server. Coba scan ulang.',
	resetTitle: 'Apakah Anda yakin akan mereset?',
	resetHtml: 'Seluruh <b>riwayat transaksi</b> dan <b>semua produk</b> akan dihapus permanen.<br>Ketik <b>RESET</b> untuk melanjutkan.',
	resetPlaceholder: 'Ketik RESET',
	resetConfirm: 'Ya, reset sekarang',
	resetValidator: 'Ketik RESET (huruf besar) untuk melanjutkan.',
	...window.KoperasiKu?.i18n,
};

const translate = (template, replacements = {}) =>
	Object.entries(replacements).reduce(
		(text, [key, value]) => text.replace(`:${key}`, value),
		template,
	);

const sidebar = document.querySelector('#app-sidebar');
const sidebarToggle = document.querySelector('#sidebar-toggle');
const sidebarBackdrop = document.querySelector('#sidebar-backdrop');

function setSidebarOpen(open) {
	if (!sidebar || !sidebarToggle || !sidebarBackdrop) return;

	sidebar.classList.toggle('-translate-x-full', !open);
	sidebarBackdrop.classList.toggle('hidden', !open);
	sidebarToggle.setAttribute('aria-expanded', String(open));
}

sidebarToggle?.addEventListener('click', () => {
	setSidebarOpen(sidebarToggle.getAttribute('aria-expanded') !== 'true');
});
sidebarBackdrop?.addEventListener('click', () => setSidebarOpen(false));

// Dropdown preferensi (tema & bahasa)
document.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
	const toggle = dropdown.querySelector('[data-dropdown-toggle]');
	const menu = dropdown.querySelector('[data-dropdown-menu]');

	toggle?.addEventListener('click', (event) => {
		event.stopPropagation();
		const open = !menu.classList.toggle('hidden');
		toggle.setAttribute('aria-expanded', String(open));
	});

	document.addEventListener('click', (event) => {
		if (dropdown.contains(event.target)) return;

		menu?.classList.add('hidden');
		toggle?.setAttribute('aria-expanded', 'false');
	});
});

const passwordIcons = {
	eye: '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>',
	eyeOff: '<path d="M3 3l18 18"/><path d="M10.6 5.1A10.9 10.9 0 0 1 12 5c6 0 9.5 7 9.5 7a17.7 17.7 0 0 1-3.3 4.3M6.6 6.7A17.6 17.6 0 0 0 2.5 12s3.5 7 9.5 7a10.5 10.5 0 0 0 4.3-.9"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>',
};

document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
	const icon = toggle.querySelector('[data-password-icon]');

	toggle.addEventListener('click', () => {
		const input = document.querySelector(toggle.dataset.passwordToggle);
		if (!input) return;

		const willShow = input.type === 'password';
		input.type = willShow ? 'text' : 'password';
		toggle.setAttribute('aria-label', willShow ? appI18n.hidePassword : appI18n.showPassword);
		toggle.setAttribute('title', willShow ? appI18n.hidePassword : appI18n.showPassword);

		if (icon) {
			icon.innerHTML = willShow ? passwordIcons.eyeOff : passwordIcons.eye;
		}
	});
});

const cashier = document.querySelector('[data-cashier]');

if (cashier) {
	const cart = new Map();
	const rows = cashier.querySelector('[data-cart-rows]');
	const inputs = cashier.querySelector('[data-cart-inputs]');
	const totalOutput = cashier.querySelector('[data-cart-total]');
	const changeOutput = cashier.querySelector('[data-cart-change]');
	const paymentInput = cashier.querySelector('[data-payment]');
	const checkoutButton = cashier.querySelector('[data-checkout-submit]');
	const checkoutForm = cashier.querySelector('[data-checkout-form]');
	const emptyMessage = cashier.querySelector('[data-cart-empty]');
	const barcodeInput = cashier.querySelector('[data-barcode-input]');
	const scanMessage = cashier.querySelector('[data-scan-message]');
	const scanEndpoint = cashier.dataset.barcodeEndpoint;
	const scanQueue = [];
	let scanning = false;
	const rupiah = new Intl.NumberFormat('id-ID');
	const formatRupiah = (value) => `Rp${rupiah.format(value)}`;
	const total = () => [...cart.values()].reduce((sum, item) => sum + item.price * item.quantity, 0);

	function focusScanner() {
		barcodeInput?.focus();
	}

	function showScanMessage(text, tone = 'muted') {
		if (!scanMessage) return;

		scanMessage.textContent = text;
		scanMessage.classList.remove('hidden', 'text-success', 'text-danger', 'text-muted');
		scanMessage.classList.add(tone === 'error' ? 'text-danger' : tone === 'success' ? 'text-success' : 'text-muted');
	}

	/**
	 * Barcode selalu diproses berurutan agar scan cepat tidak saling menimpa.
	 */
	function enqueueScan(value) {
		const code = value.trim();
		if (barcodeInput) barcodeInput.value = '';

		if (code !== '') {
			scanQueue.push(code);
			processScanQueue();
		}

		focusScanner();
	}

	async function processScanQueue() {
		if (scanning) return;
		scanning = true;

		while (scanQueue.length > 0) {
			await scanBarcode(scanQueue.shift());
		}

		scanning = false;
		focusScanner();
	}

	async function scanBarcode(code) {
		const existing = findItemByCode(code);
		const quantity = existing ? existing.quantity : 0;

		try {
			const response = await fetch(`${scanEndpoint}?code=${encodeURIComponent(code)}&quantity=${quantity}`, {
				headers: { Accept: 'application/json' },
			});
			const payload = await response.json();

			if (!response.ok) {
				showScanMessage(payload.message ?? appI18n.scanError, 'error');
				return;
			}

			if (existing) {
				existing.name = payload.product.name;
				existing.price = payload.product.price;
				existing.stock = payload.product.stock;
				existing.quantity = payload.quantity;
				cart.set(existing.id, existing);
			} else {
				cart.set(payload.product.id, {
					id: payload.product.id,
					code: payload.product.code,
					name: payload.product.name,
					price: payload.product.price,
					stock: payload.product.stock,
					quantity: payload.quantity,
				});
			}

			showScanMessage(payload.message, 'success');
			renderCart();
		} catch (error) {
			showScanMessage(appI18n.scanFailed, 'error');
		}
	}

	function findItemByCode(code) {
		const needle = code.toUpperCase();

		return [...cart.values()].find((item) => String(item.code ?? '').toUpperCase() === needle);
	}

	function renderCart() {
		rows.replaceChildren();
		inputs.replaceChildren();
		emptyMessage.classList.toggle('hidden', cart.size > 0);

		[...cart.values()].forEach((item, index) => {
			const row = document.createElement('tr');
			const productCell = document.createElement('td');
			const quantityCell = document.createElement('td');
			const priceCell = document.createElement('td');
			const subtotalCell = document.createElement('td');
			const actionCell = document.createElement('td');
			const quantityControl = document.createElement('div');
			const decrement = document.createElement('button');
			const quantity = document.createElement('span');
			const increment = document.createElement('button');
			const remove = document.createElement('button');

			productCell.className = 'font-medium';
			productCell.textContent = item.name;
			quantityControl.className = 'inline-flex items-center gap-2';
			decrement.className = 'quantity-button';
			decrement.type = 'button';
			decrement.textContent = '−';
			decrement.setAttribute('aria-label', translate(appI18n.decrease, { name: item.name }));
			decrement.addEventListener('click', () => changeQuantity(item.id, -1));
			quantity.className = 'min-w-5 text-center text-sm tabular';
			quantity.textContent = String(item.quantity);
			increment.className = 'quantity-button';
			increment.type = 'button';
			increment.textContent = '+';
			increment.setAttribute('aria-label', translate(appI18n.increase, { name: item.name }));
			increment.disabled = item.quantity >= item.stock;
			increment.addEventListener('click', () => changeQuantity(item.id, 1));
			quantityControl.append(decrement, quantity, increment);
			quantityCell.append(quantityControl);
			priceCell.className = 'whitespace-nowrap text-muted';
			priceCell.textContent = formatRupiah(item.price);
			subtotalCell.className = 'whitespace-nowrap font-semibold tabular';
			subtotalCell.textContent = formatRupiah(item.price * item.quantity);
			remove.className = 'btn-ghost btn-sm text-danger';
			remove.type = 'button';
			remove.textContent = appI18n.remove;
			remove.addEventListener('click', () => {
				cart.delete(item.id);
				renderCart();
			});
			actionCell.append(remove);
			row.append(productCell, quantityCell, priceCell, subtotalCell, actionCell);
			rows.append(row);

			[['id', item.id], ['quantity', item.quantity]].forEach(([field, value]) => {
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = `cart[${index}][${field}]`;
				hiddenInput.value = String(value);
				inputs.append(hiddenInput);
			});
		});

		totalOutput.textContent = formatRupiah(total());
		updatePayment();
	}

	function changeQuantity(id, difference) {
		const item = cart.get(id);
		if (!item) return;

		item.quantity += difference;
		if (item.quantity < 1) cart.delete(id);
		renderCart();
	}

	function updatePayment() {
		const paid = Number(paymentInput.value) || 0;
		const balance = paid - total();
		changeOutput.textContent = formatRupiah(Math.max(balance, 0));
		checkoutButton.disabled = cart.size === 0 || balance < 0;
	}

	cashier.querySelectorAll('[data-add-product]').forEach((button) => {
		button.addEventListener('click', () => {
			const id = Number(button.dataset.id);
			const item = cart.get(id) ?? {
				id,
				code: button.dataset.code,
				name: button.dataset.name,
				price: Number(button.dataset.price),
				stock: Number(button.dataset.stock),
				quantity: 0,
			};

			if (item.quantity < item.stock) {
				item.quantity += 1;
				cart.set(id, item);
				renderCart();
			} else {
				showScanMessage(appI18n.stockMax, 'error');
			}
		});
	});

	barcodeInput?.addEventListener('keydown', (event) => {
		if (event.key === 'Enter') {
			event.preventDefault();
			enqueueScan(barcodeInput.value);
		}
	});

	// Klik di area netral selalu mengembalikan fokus ke scanner agar siap scan berikutnya.
	document.addEventListener('click', (event) => {
		if (!barcodeInput || event.target.closest('input, button, a, select, textarea, label')) return;

		focusScanner();
	});

	paymentInput.addEventListener('input', updatePayment);
	checkoutForm.addEventListener('submit', (event) => {
		if (checkoutButton.disabled) event.preventDefault();
	});
	renderCart();
	focusScanner();
}

const bulkSelectAll = document.querySelector('[data-select-all]');
const bulkCheckboxes = document.querySelectorAll('[data-product-checkbox]');
const bulkButton = document.querySelector('[data-bulk-delete]');
const bulkCount = document.querySelector('[data-selected-count]');

if (bulkSelectAll && bulkCheckboxes.length > 0) {
	const updateBulkState = () => {
		const selected = [...bulkCheckboxes].filter((checkbox) => checkbox.checked).length;

		if (bulkCount) bulkCount.textContent = String(selected);
		if (bulkButton) bulkButton.disabled = selected === 0;

		bulkSelectAll.checked = selected > 0 && selected === bulkCheckboxes.length;
		bulkSelectAll.indeterminate = selected > 0 && selected < bulkCheckboxes.length;
	};

	bulkSelectAll.addEventListener('change', () => {
		bulkCheckboxes.forEach((checkbox) => {
			checkbox.checked = bulkSelectAll.checked;
		});

		updateBulkState();
	});

	bulkCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateBulkState));

	updateBulkState();
}

/*
 * SweetAlert2: konfirmasi tindakan (hapus, reset, dsb.) dan notifikasi flash.
 * Tema mengikuti mode terang/gelap yang aktif pada <html data-theme>.
 */
const sweetAlertDefaults = {
	theme: document.documentElement.dataset.theme === 'dark' ? 'dark' : 'default',
	confirmButtonColor: '#2563eb',
	cancelButtonColor: '#64748b',
	reverseButtons: true,
};

document.querySelectorAll('form[data-confirm]').forEach((form) => {
	const tone = form.dataset.confirmTone ?? 'primary';

	form.addEventListener('submit', (event) => {
		if (form.dataset.confirmed === 'true') {
			return;
		}

		event.preventDefault();

		Swal.fire({
			...sweetAlertDefaults,
			title: form.dataset.confirmTitle ?? appI18n.confirm,
			text: form.dataset.confirm,
			icon: tone === 'danger' ? 'warning' : 'question',
			showCancelButton: true,
			confirmButtonText: form.dataset.confirmButton ?? appI18n.yesContinue,
			cancelButtonText: appI18n.cancel,
			confirmButtonColor: tone === 'danger' ? '#dc2626' : sweetAlertDefaults.confirmButtonColor,
		}).then((result) => {
			if (result.isConfirmed) {
				form.dataset.confirmed = 'true';
				form.submit();
			}
		});
	});
});

const resetTrigger = document.querySelector('[data-reset-trigger]');

if (resetTrigger) {
	const resetForm = resetTrigger.closest('form');
	const confirmationInput = resetForm?.querySelector('input[name="confirmation"]');

	resetTrigger.addEventListener('click', () => {
		Swal.fire({
			...sweetAlertDefaults,
			title: appI18n.resetTitle,
			html: appI18n.resetHtml,
			icon: 'warning',
			input: 'text',
			inputPlaceholder: appI18n.resetPlaceholder,
			inputAttributes: { autocapitalize: 'characters', autocomplete: 'off' },
			showCancelButton: true,
			confirmButtonText: appI18n.resetConfirm,
			cancelButtonText: appI18n.cancel,
			confirmButtonColor: '#dc2626',
			focusCancel: true,
			inputValidator: (value) => (value === 'RESET' ? null : appI18n.resetValidator),
		}).then((result) => {
			if (result.isConfirmed && resetForm && confirmationInput) {
				confirmationInput.value = result.value;
				resetForm.submit();
			}
		});
	});
}

const flashNotices = document.querySelectorAll('[data-flash]');

if (flashNotices.length > 0) {
	const Toast = Swal.mixin({
		toast: true,
		position: 'top-end',
		showConfirmButton: false,
		timer: 4000,
		timerProgressBar: true,
		didOpen: (toast) => {
			toast.addEventListener('mouseenter', Swal.stopTimer);
			toast.addEventListener('mouseleave', Swal.resumeTimer);
		},
	});

	flashNotices.forEach((notice) => {
		const type = notice.dataset.flash === 'error' ? 'error' : 'success';
		notice.classList.add('hidden');
		Toast.fire({ icon: type, title: notice.textContent.trim() });
	});
}
