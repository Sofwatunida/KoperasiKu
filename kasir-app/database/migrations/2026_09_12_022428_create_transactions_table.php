public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_number');
        $table->integer('total_price');
        $table->integer('pay_amount');
        $table->json('items'); // Menyimpan detail produk belanjaan
        $table->timestamps();
    });
}
