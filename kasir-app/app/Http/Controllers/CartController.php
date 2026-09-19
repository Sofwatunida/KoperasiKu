namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index() {
        $products = Product::where('stock', '>', 0)->get();
        $cart = session()->get('cart', []);
        
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cashier.index', compact('products', 'cart', 'total'));
    }

    public function add($id) {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back();
    }

    public function updateCart(Request $request) {
        $cart = session()->get('cart', []);
        if($request->id && $request->quantity) {
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
        }
        return redirect()->back();
    }

    public function remove($id) {
        $cart = session()->get('cart', []);
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back();
    }

    public function checkout(Request $request) {
        $cart = session()->get('cart', []);
        if(empty($cart)) return redirect()->back()->with('error', 'Keranjang kosong!');

        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if($request->pay_amount < $total) {
            return redirect()->back()->with('error', 'Uang pembayaran kurang!');
        }

        // Potong stok produk
        foreach($cart as $id => $item) {
            $product = Product::find($id);
            $product->decrement('stock', $item['quantity']);
        }

        // Simpan transaksi
        $transaction = Transaction::create([
            'invoice_number' => 'INV-' . time(),
            'total_price' => $total,
            'pay_amount' => $request->pay_amount,
            'items' => json_encode($cart)
        ]);

        session()->forget('cart');

        return redirect()->route('cashier.receipt', $transaction->id);
    }

    public function receipt($id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->items = json_decode($transaction->items, true);
        return view('cashier.receipt', compact('transaction'));
    }
}
