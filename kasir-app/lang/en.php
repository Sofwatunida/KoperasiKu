<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Common
    |--------------------------------------------------------------------------
    */

    'common' => [
        'app_name' => 'KoperasiKu',
        'tagline' => 'Cooperative Cashier & Management',
        'search' => 'Search',
        'reset' => 'Reset',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'cancel' => 'Cancel',
        'back' => 'Back',
        'detail' => 'Detail',
        'no' => 'No',
        'status' => 'Status',
        'action' => 'Actions',
        'total' => 'Total',
        'yes_continue' => 'Yes, continue',
        'confirm' => 'Confirmation',
        'officer' => 'Officer',
        'deleted_product' => 'Deleted product',
        'show_password' => 'Show password',
        'hide_password' => 'Hide password',
        'not_found_hint' => 'Try another keyword.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation & Layout
    |--------------------------------------------------------------------------
    */

    'nav' => [
        'main_menu' => 'Main menu',
        'dashboard' => 'Dashboard',
        'products' => 'Products',
        'transactions' => 'Transactions',
        'reports' => 'Reports',
        'users' => 'Users',
        'settings' => 'Settings',
        'logout' => 'Sign out',
        'open_menu' => 'Open menu',
        'close_menu' => 'Close menu',
        'collapse' => 'Hide sidebar',
        'expand' => 'Show sidebar',
    ],

    'header' => [
        'subtitle' => 'Cooperative Cashier & Management System',
        'low_stock' => 'Products with low stock',
        'view_low_stock' => 'View products with low stock',
    ],

    'preferences' => [
        'title' => 'Display preferences',
        'theme' => 'Theme',
        'light' => 'Light',
        'dark' => 'Dark',
        'language' => 'Language',
    ],

    /*
    |--------------------------------------------------------------------------
    | Table labels
    |--------------------------------------------------------------------------
    */

    'table' => [
        'transaction_id' => 'Transaction ID',
        'date' => 'Date',
        'cashier' => 'Cashier',
        'total' => 'Total',
        'code' => 'Code',
        'product_name' => 'Product Name',
        'selling_price' => 'Selling Price',
        'purchase_price' => 'Purchase Price',
        'stock' => 'Stock',
        'unit' => 'Unit',
        'name' => 'Name',
        'username' => 'Username',
        'email' => 'Email',
        'role' => 'Role',
        'qty' => 'Qty',
        'price' => 'Price',
        'product' => 'Product',
        'subtotal' => 'Subtotal',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'welcome' => 'Welcome, :name.',
        'subtitle' => "Today's cooperative activity summary.",
        'stat_total_products' => 'Total Products',
        'stat_products_suffix' => 'Products',
        'stat_stock' => 'Stock Items',
        'stat_stock_suffix' => 'Items',
        'stat_today_transactions' => "Today's Transactions",
        'stat_transactions_suffix' => 'Transactions',
        'stat_today_revenue' => "Today's Revenue",
        'stat_revenue_suffix' => 'Total Sales',
        'chart_title' => 'Sales Chart',
        'chart_subtitle' => 'Last 7 days',
        'chart_alt' => 'Sales chart for the last seven days',
        'low_stock_title' => 'Stock to Watch',
        'low_stock_subtitle' => ':threshold or less in stock',
        'all_products' => 'All products',
        'stock_normal' => 'All stock levels are normal.',
        'latest_transactions' => 'Recent Transactions',
        'latest_subtitle' => 'Latest sales activity',
        'view_all' => 'View all',
        'no_transactions' => 'No transactions yet.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    'products' => [
        'title' => 'Products',
        'page_title' => 'Product Data',
        'page_subtitle' => 'Manage the cooperative catalog, prices, and stock.',
        'add' => 'Add Product',
        'list_title' => 'Product List',
        'registered' => ':count registered products',
        'search_placeholder' => 'Search product name or code...',
        'low_stock_notice' => 'Showing products with a stock of :threshold or less.',
        'select_hint' => 'Check the products you want to delete, then click this button.',
        'delete_selected' => 'Delete Selected',
        'delete_selected_count' => ':count products',
        'delete_selected_confirm' => 'Delete the selected products? Products with transaction history will not be deleted.',
        'delete_selected_title' => 'Delete selected products',
        'delete_confirm' => 'Are you sure you want to delete this product?',
        'delete_title' => 'Delete product',
        'empty' => 'No products yet.',
        'empty_hint' => "Add the cooperative's first product.",
        'not_found' => 'Product not found.',
        'create_title' => 'Add Product',
        'create_heading' => 'Add New Product',
        'create_subtitle' => 'Enter the cooperative product data.',
        'edit_title' => 'Edit Product',
        'edit_subtitle' => 'Update information for :name.',
        'save' => 'Save Product',
        'update' => 'Update Product',
        'created' => 'Product created successfully.',
        'updated' => 'Product updated successfully.',
        'deleted' => 'Product deleted successfully.',
        'delete_blocked' => 'The product cannot be deleted because it has transaction history.',
        'bulk_min' => 'Select at least one product to delete.',
        'bulk_missing' => 'Some of the selected products were not found.',
        'bulk_none_deleted' => 'No products were deleted.',
        'bulk_all_blocked' => 'No products were deleted. The selected products have transaction history.',
        'bulk_success' => ':count products deleted successfully.',
        'bulk_blocked' => ':count products skipped because they have transaction history.',
    ],

    'product_form' => [
        'title' => 'Product Information',
        'new_title' => 'New Product Information',
        'hint' => 'Fill in the product data correctly.',
        'code' => 'Product Code',
        'name' => 'Product Name',
        'purchase_price' => 'Purchase Price',
        'selling_price' => 'Selling Price',
        'stock' => 'Stock',
        'unit' => 'Unit',
    ],

    'stock' => [
        'out' => 'Out of stock',
        'low' => 'Low stock',
        'normal' => 'Normal stock',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cashier
    |--------------------------------------------------------------------------
    */

    'cashier' => [
        'page_title' => 'Cashier',
        'page_subtitle' => 'Scan a barcode or select products, review the cart, then complete the payment.',
        'scan_title' => 'Barcode Scan',
        'scan_hint' => 'Point the scanner at the product barcode. This field is always ready to scan.',
        'scan_badge' => 'Automatic',
        'barcode_label' => 'Product barcode',
        'barcode_placeholder' => 'Scan or type a barcode then press Enter...',
        'barcode_required' => 'Barcode is required.',
        'scan_error' => 'The barcode could not be processed.',
        'scan_failed' => 'Failed to reach the server. Try scanning again.',
        'scan_not_found' => 'No product found with that barcode.',
        'scan_stock_max' => 'Quantity has reached the maximum stock.',
        'scan_added' => ':name added to the cart.',
        'find_title' => 'Find / Select Products',
        'find_hint' => 'Use search or the add button on products.',
        'manage_products' => 'Manage products',
        'no_products' => 'No matching products.',
        'add_label' => 'Add :name',
        'cart_title' => 'Cart',
        'cart_hint' => 'Quantity and subtotal update automatically.',
        'history' => 'History',
        'cart_empty' => 'The cart is empty.',
        'paid_label' => 'Payment amount',
        'paid_placeholder' => 'Enter the payment amount',
        'paid_required' => 'Payment amount is required.',
        'change_label' => 'Change',
        'pay' => 'Pay & Save Transaction',
        'increase' => 'Increase :name',
        'decrease' => 'Decrease :name',
        'remove' => 'Remove',
        'stock_insufficient' => 'Insufficient stock for :name.',
        'paid_insufficient' => 'The payment amount is not enough.',
        'checkout_success' => 'Transaction saved successfully.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction History & Detail
    |--------------------------------------------------------------------------
    */

    'transactions' => [
        'title' => 'Transaction History',
        'page_subtitle' => 'Browse the transactions recorded by the cooperative.',
        'new' => 'New Transaction',
        'list_title' => 'Transaction List',
        'search_placeholder' => 'Search transaction ID',
        'empty' => 'No transactions yet.',
        'not_found' => 'Transaction not found.',
    ],

    'transaction' => [
        'title' => 'Transaction Detail',
        'number' => 'Transaction No.',
        'date' => 'Date',
        'product' => 'Product',
        'quantity' => 'Qty',
        'price' => 'Price',
        'subtotal' => 'Subtotal',
        'paid' => 'Paid',
        'change' => 'Change',
        'print_receipt' => 'Print Receipt',
    ],

    /*
    |--------------------------------------------------------------------------
    | Receipt (58mm)
    |--------------------------------------------------------------------------
    */

    'receipt' => [
        'title' => 'Receipt',
        'back_to_cashier' => 'Back to cashier',
        'print' => 'Print Receipt',
        'paper_hint' => '58mm paper size',
    ],

    'struk' => [
        'shop_tagline' => 'School Cooperative',
        'total' => 'TOTAL',
        'paid' => 'PAID',
        'change' => 'CHANGE',
        'thanks' => 'THANK YOU',
        'thanks_sub' => 'Thank you for shopping',
    ],

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    'users' => [
        'title' => 'User Management',
        'page_subtitle' => 'Manage super admin, admin, and cashier accounts.',
        'add' => 'Add User',
        'list_title' => 'User List',
        'super_admin' => 'super admin',
        'admin' => 'admin',
        'kasir' => 'cashier',
        'search_placeholder' => 'Search name, username, or email...',
        'delete_confirm' => 'Are you sure you want to delete this user?',
        'delete_title' => 'Delete user',
        'empty' => 'No users yet.',
        'empty_hint' => 'Add the first user.',
        'not_found' => 'User not found.',
        'create_title' => 'Add User',
        'create_heading' => 'Add New User',
        'create_subtitle' => 'Create a super admin, admin, or cashier account.',
        'edit_title' => 'Edit User',
        'edit_subtitle' => 'Update account data, role, or password.',
        'save' => 'Save User',
        'update' => 'Update User',
        'created' => 'User created successfully.',
        'updated' => 'User updated successfully.',
        'deleted' => 'User deleted successfully.',
        'cannot_delete_self' => 'You cannot delete your own account.',
        'last_admin' => 'At least one admin must remain active.',
        'last_super_admin' => 'At least one super admin must remain active.',
        'has_transactions' => 'The account cannot be deleted because it has transaction history.',
        'super_admin_only' => 'Super admin accounts can only be managed by a super admin.',
        'admin_manages_kasir_only' => 'Admin can only manage cashier accounts.',
        'username_taken' => 'Username is already taken.',
    ],

    'user_form' => [
        'title' => 'User Information',
        'new_title' => 'New User Information',
        'hint' => 'The role determines access: super admin for full access, admin for managers, cashier for transactions.',
        'name' => 'Full Name',
        'username_pattern' => 'Only letters, numbers, underscores, and dashes.',
        'password_keep' => 'Leave blank to keep current',
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'title' => 'Settings',
        'page_title' => 'Account Settings',
        'page_subtitle' => 'Update your email, password, and account information.',
        'info_title' => 'Account Information',
        'role_hint' => 'Account role: :role · the role cannot be changed from this page.',
        'field_name' => 'Name',
        'field_username' => 'Username',
        'field_email' => 'Email',
        'current_password' => 'Current Password',
        'current_password_placeholder' => 'Required to change email or password',
        'current_password_hint' => 'Only required when changing your email or password.',
        'password_title' => 'Change Password',
        'password_hint' => 'Leave blank to keep your current password.',
        'new_password' => 'New Password',
        'new_password_placeholder' => 'At least 8 characters',
        'confirm_password' => 'Confirm New Password',
        'save' => 'Save Changes',
        'updated' => 'Settings updated successfully.',
        'email_taken' => 'Email is already used by another account.',
        'invalid_email' => 'Invalid email format.',
        'password_min' => 'Password must be at least 8 characters.',
        'password_mismatch' => 'The new password confirmation does not match.',
        'current_password_invalid' => 'The current password is incorrect.',
        'reset_confirmation_required' => 'Type RESET to confirm the data reset.',
        'reset_confirmation_invalid' => 'Wrong confirmation. Type RESET (uppercase) to continue.',
        'reset_success' => 'Reset successful. :transactions transactions and :products products have been deleted.',
        'import_success' => 'Import successful. :users users, :products products, and :transactions transactions restored.',
        'import_invalid_file' => 'The backup file cannot be read.',
    ],

    'backup' => [
        'title' => 'Backup Data',
        'description' => 'Download all user, product, and transaction data as a single backup (.json) file.',
        'hint' => 'Keep the backup file in a safe place. It can be restored through the Import Data section.',
        'button' => 'Download Backup',
    ],

    'import' => [
        'title' => 'Import Data',
        'description' => 'Restore data from a backup file. All current data will be replaced by the file contents.',
        'file_label' => 'Backup file (.json)',
        'button' => 'Import & Restore',
        'confirm' => 'All user, product, and transaction data will be replaced by the backup file contents. Continue?',
        'confirm_title' => 'Import backup data',
        'confirm_button' => 'Yes, import now',
        'file_required' => 'Choose the backup file to import.',
        'invalid' => 'The backup file is invalid or the format is not supported.',
        'invalid_structure' => 'The backup file structure is incomplete.',
    ],

    'reset' => [
        'title' => 'Reset Data',
        'description' => 'Empties all transaction history and products so the cooperative starts fresh. User accounts are not affected.',
        'warning' => 'All transaction history and product data will be permanently deleted and cannot be undone. You will be asked to type <b>RESET</b> to confirm.',
        'button' => 'Reset All Data',
        'confirm_title' => 'Are you sure you want to reset?',
        'confirm_html' => 'All <b>transaction history</b> and <b>all products</b> will be permanently deleted.<br>Type <b>RESET</b> to continue.',
        'placeholder' => 'Type RESET',
        'confirm_button' => 'Yes, reset now',
        'validator' => 'Type RESET (uppercase) to continue.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'login' => [
        'title' => 'Sign In',
        'hero_tagline' => 'Cooperative cashier & management',
        'hero_heading' => 'Everything your cooperative needs, in one place.',
        'hero_text' => 'Manage products, serve transactions, and monitor daily sales more neatly.',
        'summary_label' => 'Sales summary',
        'today' => 'Today',
        'heading' => 'Sign in to your account',
        'subtitle' => 'Please sign in to continue.',
        'identifier' => 'Email or username',
        'submit' => 'Sign In',
        'no_account' => "Don't have an account?",
        'register' => 'Register',
        'identifier_required' => 'Email or username is required.',
        'invalid_credentials' => 'Email/username or password is incorrect.',
        'logged_out' => 'You have been signed out.',
        'registered' => 'Account created successfully.',
    ],

    'register' => [
        'title' => 'Register',
        'hero_heading' => 'Create a cooperative staff account.',
        'hero_text' => 'New accounts get staff access. Admin access can only be granted by a manager.',
        'heading' => 'Create a new account',
        'subtitle' => 'Register a staff account to start using the application.',
        'name' => 'Full name',
        'username' => 'Username',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'submit' => 'Register as Staff',
        'have_account' => 'Already have an account?',
        'login' => 'Sign in',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    'report' => [
        'title' => 'Sales Report',
        'subtitle' => 'Transaction summary by period.',
        'unknown_cashier' => 'Officer',
        'deleted_product' => 'Deleted product',
        'period' => 'Period',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'show' => 'Show',
        'print' => 'Print Report',
        'export_label' => 'Export report:',
        'export_pdf' => 'Export PDF',
        'export_excel' => 'Export Excel',
        'export_word' => 'Export Word',
        'cashier' => 'Cashier',
        'all_cashiers' => 'All cashiers',
        'total_transactions' => 'Total Transactions',
        'total_revenue' => 'Total Revenue',
        'items_sold' => 'Items Sold',
        'average' => 'Average / Transaction',
        'transactions_this_period' => 'Transactions in this period',
        'from_saved_transactions' => 'From saved transactions',
        'daily_sales' => 'Daily Sales',
        'selected_period' => 'Selected period',
        'transactions_table' => 'Transactions in this Period',
        'top_products' => 'Top Products',
        'top_products_hint' => 'Top 5 products by quantity sold',
        'rank' => 'Rank',
        'qty_sold' => 'Qty Sold',
        'back' => 'Back',
        'no_transactions' => 'No transactions in this period.',
        'columns' => [
            'date' => 'Date',
            'code' => 'Code',
            'cashier' => 'Cashier',
            'product' => 'Product',
            'qty' => 'Qty',
            'price' => 'Price',
            'subtotal' => 'Subtotal',
        ],
        'summary' => [
            'total_transactions' => 'Total Transactions',
            'total_revenue' => 'Total Revenue',
        ],
    ],

];
