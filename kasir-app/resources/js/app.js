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

document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
	toggle.addEventListener('click', () => {
		const input = document.querySelector(toggle.dataset.passwordToggle);
		if (!input) return;

		const showing = input.type === 'password';
		input.type = showing ? 'text' : 'password';
		toggle.setAttribute('aria-label', showing ? 'Sembunyikan password' : 'Tampilkan password');
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
	const rupiah = new Intl.NumberFormat('id-ID');
	const formatRupiah = (value) => `Rp${rupiah.format(value)}`;
	const total = () => [...cart.values()].reduce((sum, item) => sum + item.price * item.quantity, 0);

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
			decrement.setAttribute('aria-label', `Kurangi ${item.name}`);
			decrement.addEventListener('click', () => changeQuantity(item.id, -1));
			quantity.className = 'min-w-5 text-center text-sm tabular';
			quantity.textContent = String(item.quantity);
			increment.className = 'quantity-button';
			increment.type = 'button';
			increment.textContent = '+';
			increment.setAttribute('aria-label', `Tambah ${item.name}`);
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
			remove.textContent = 'Hapus';
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
				name: button.dataset.name,
				price: Number(button.dataset.price),
				stock: Number(button.dataset.stock),
				quantity: 0,
			};

			if (item.quantity < item.stock) {
				item.quantity += 1;
				cart.set(id, item);
				renderCart();
			}
		});
	});

	paymentInput.addEventListener('input', updatePayment);
	checkoutForm.addEventListener('submit', (event) => {
		if (checkoutButton.disabled) event.preventDefault();
	});
	renderCart();
}
