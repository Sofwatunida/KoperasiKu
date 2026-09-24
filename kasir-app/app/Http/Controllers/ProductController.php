namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required', 'price' => 'required|numeric', 'stock' => 'required|numeric']);
        Product::create($request->all());
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product) {
        $request->validate(['name' => 'required', 'price' => 'required|numeric', 'stock' => 'required|numeric']);
        $product->update($request->all());
        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product) {
        $product->delete();
        return redirect()->back()->with('success', 'Produk berhasil dihapus.');
    }
}
