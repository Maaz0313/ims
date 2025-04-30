<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories'
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Stationery and office equipment'
            ],
            [
                'name' => 'Furniture',
                'description' => 'Office and home furniture'
            ],
            [
                'name' => 'Computer Hardware',
                'description' => 'Computer components and peripherals'
            ],
            [
                'name' => 'Software',
                'description' => 'Computer software and licenses'
            ],
            [
                'name' => 'Networking',
                'description' => 'Networking equipment and accessories'
            ],
            [
                'name' => 'Mobile Accessories',
                'description' => 'Accessories for mobile phones and tablets'
            ],
            [
                'name' => 'Printers & Scanners',
                'description' => 'Printing and scanning devices'
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
            ]);
        }

        // Create suppliers if they don't exist
        $suppliers = [
            [
                'name' => 'TechWorld Distributors',
                'contact_person' => 'John Smith',
                'email' => 'john@techworld.com',
                'phone' => '555-123-4567',
                'address' => '123 Tech Blvd, Silicon Valley, CA 94043'
            ],
            [
                'name' => 'Office Solutions Inc.',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@officesolutions.com',
                'phone' => '555-987-6543',
                'address' => '456 Office Park, Business District, NY 10001'
            ],
            [
                'name' => 'Global Electronics',
                'contact_person' => 'Michael Chen',
                'email' => 'michael@globalelectronics.com',
                'phone' => '555-456-7890',
                'address' => '789 Global Ave, International City, TX 75001'
            ],
            [
                'name' => 'Network Systems Ltd',
                'contact_person' => 'Emma Wilson',
                'email' => 'emma@networksystems.com',
                'phone' => '555-789-0123',
                'address' => '321 Network Drive, Tech City, WA 98001'
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Get category and supplier IDs
        $categoryIds = Category::pluck('id', 'name')->toArray();
        $supplierIds = Supplier::pluck('id', 'name')->toArray();

        // Create products
        $products = [
            // Electronics
            [
                'name' => 'Dell XPS 15 Laptop',
                'description' => '15.6" 4K UHD, Intel Core i7, 16GB RAM, 512GB SSD',
                'sku' => 'DELL-XPS15-001',
                'category_id' => $categoryIds['Electronics'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 1200.00,
                'price' => 1499.99,
                'quantity' => 15
            ],
            [
                'name' => 'Apple MacBook Pro',
                'description' => '13" Retina Display, M1 Chip, 8GB RAM, 256GB SSD',
                'sku' => 'APPLE-MBP-001',
                'category_id' => $categoryIds['Electronics'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 950.00,
                'price' => 1299.99,
                'quantity' => 10
            ],
            [
                'name' => 'Samsung 32" 4K Monitor',
                'description' => '32" UHD 4K Display, HDMI, DisplayPort',
                'sku' => 'SMSNG-MON-001',
                'category_id' => $categoryIds['Electronics'],
                'supplier_id' => $supplierIds['Global Electronics'],
                'cost_price' => 320.00,
                'price' => 399.99,
                'quantity' => 20
            ],
            [
                'name' => 'Logitech MX Master 3 Mouse',
                'description' => 'Wireless Mouse with Customizable Buttons',
                'sku' => 'LOG-MXMST-001',
                'category_id' => $categoryIds['Electronics'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 70.00,
                'price' => 99.99,
                'quantity' => 30
            ],
            [
                'name' => 'Sony WH-1000XM4 Headphones',
                'description' => 'Wireless Noise Cancelling Headphones',
                'sku' => 'SONY-HDPHN-001',
                'category_id' => $categoryIds['Electronics'],
                'supplier_id' => $supplierIds['Global Electronics'],
                'cost_price' => 250.00,
                'price' => 349.99,
                'quantity' => 25
            ],

            // Office Supplies
            [
                'name' => 'HP Premium Printer Paper',
                'description' => 'Letter Size, 8.5" x 11", 500 Sheets/Ream',
                'sku' => 'HP-PAPER-001',
                'category_id' => $categoryIds['Office Supplies'],
                'supplier_id' => $supplierIds['Office Solutions Inc.'],
                'cost_price' => 4.50,
                'price' => 8.99,
                'quantity' => 100
            ],
            [
                'name' => 'Staples Standard Stapler',
                'description' => 'Desktop Stapler with 20 Sheet Capacity',
                'sku' => 'STPL-STPLR-001',
                'category_id' => $categoryIds['Office Supplies'],
                'supplier_id' => $supplierIds['Office Solutions Inc.'],
                'cost_price' => 5.00,
                'price' => 9.99,
                'quantity' => 50
            ],
            [
                'name' => 'Post-it Notes Value Pack',
                'description' => '12 Pads, 3" x 3", Assorted Colors',
                'sku' => 'PSTIT-NOTES-001',
                'category_id' => $categoryIds['Office Supplies'],
                'supplier_id' => $supplierIds['Office Solutions Inc.'],
                'cost_price' => 7.50,
                'price' => 12.99,
                'quantity' => 75
            ],
            [
                'name' => 'Sharpie Permanent Markers',
                'description' => 'Fine Point, Black, 12-Pack',
                'sku' => 'SHRP-MARK-001',
                'category_id' => $categoryIds['Office Supplies'],
                'supplier_id' => $supplierIds['Office Solutions Inc.'],
                'cost_price' => 6.00,
                'price' => 10.99,
                'quantity' => 60
            ],

            // Furniture
            [
                'name' => 'Herman Miller Aeron Chair',
                'description' => 'Ergonomic Office Chair, Size B, Graphite',
                'sku' => 'HM-AERON-001',
                'category_id' => $categoryIds['Furniture'],
                'supplier_id' => $supplierIds['Office Solutions Inc.'],
                'cost_price' => 800.00,
                'price' => 1099.99,
                'quantity' => 5
            ],
            [
                'name' => 'Adjustable Standing Desk',
                'description' => 'Electric Height Adjustable Desk, 60" x 30"',
                'sku' => 'STAND-DESK-001',
                'category_id' => $categoryIds['Furniture'],
                'supplier_id' => $supplierIds['Office Solutions Inc.'],
                'cost_price' => 350.00,
                'price' => 499.99,
                'quantity' => 8
            ],
            [
                'name' => 'Filing Cabinet',
                'description' => '3-Drawer Metal Filing Cabinet, Letter Size',
                'sku' => 'FILE-CAB-001',
                'category_id' => $categoryIds['Furniture'],
                'supplier_id' => $supplierIds['Office Solutions Inc.'],
                'cost_price' => 120.00,
                'price' => 179.99,
                'quantity' => 12
            ],

            // Computer Hardware
            [
                'name' => 'NVIDIA GeForce RTX 3080',
                'description' => 'Graphics Card, 10GB GDDR6X',
                'sku' => 'NVDA-3080-001',
                'category_id' => $categoryIds['Computer Hardware'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 650.00,
                'price' => 799.99,
                'quantity' => 7
            ],
            [
                'name' => 'Intel Core i9-11900K',
                'description' => 'Desktop Processor, 8 Cores, 16 Threads',
                'sku' => 'INTL-I9-001',
                'category_id' => $categoryIds['Computer Hardware'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 450.00,
                'price' => 549.99,
                'quantity' => 15
            ],
            [
                'name' => 'Samsung 1TB 970 EVO Plus SSD',
                'description' => 'NVMe M.2 Internal SSD',
                'sku' => 'SMSNG-SSD-001',
                'category_id' => $categoryIds['Computer Hardware'],
                'supplier_id' => $supplierIds['Global Electronics'],
                'cost_price' => 120.00,
                'price' => 169.99,
                'quantity' => 25
            ],
            [
                'name' => 'Corsair Vengeance 32GB RAM',
                'description' => 'DDR4 3600MHz CL18 Memory Kit (2x16GB)',
                'sku' => 'CORS-RAM-001',
                'category_id' => $categoryIds['Computer Hardware'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 130.00,
                'price' => 179.99,
                'quantity' => 20
            ],

            // Software
            [
                'name' => 'Microsoft Office 365',
                'description' => 'Annual Subscription, 1 User',
                'sku' => 'MS-OFF365-001',
                'category_id' => $categoryIds['Software'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 60.00,
                'price' => 99.99,
                'quantity' => 50
            ],
            [
                'name' => 'Adobe Creative Cloud',
                'description' => 'Annual Subscription, All Apps',
                'sku' => 'ADBE-CC-001',
                'category_id' => $categoryIds['Software'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 400.00,
                'price' => 599.99,
                'quantity' => 30
            ],
            [
                'name' => 'Windows 11 Pro',
                'description' => 'Operating System, Digital License',
                'sku' => 'MS-WIN11-001',
                'category_id' => $categoryIds['Software'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 120.00,
                'price' => 199.99,
                'quantity' => 40
            ],

            // Networking
            [
                'name' => 'TP-Link Archer AX6000',
                'description' => 'Wi-Fi 6 Router, Dual-Band',
                'sku' => 'TPLNK-RTR-001',
                'category_id' => $categoryIds['Networking'],
                'supplier_id' => $supplierIds['Network Systems Ltd'],
                'cost_price' => 220.00,
                'price' => 299.99,
                'quantity' => 15
            ],
            [
                'name' => 'Netgear 8-Port Gigabit Switch',
                'description' => 'Unmanaged Network Switch, 10/100/1000Mbps',
                'sku' => 'NTGR-SWTCH-001',
                'category_id' => $categoryIds['Networking'],
                'supplier_id' => $supplierIds['Network Systems Ltd'],
                'cost_price' => 35.00,
                'price' => 49.99,
                'quantity' => 25
            ],
            [
                'name' => 'Cat 6 Ethernet Cable',
                'description' => '50ft, RJ45, Network Patch Cable',
                'sku' => 'CAT6-CBL-001',
                'category_id' => $categoryIds['Networking'],
                'supplier_id' => $supplierIds['Network Systems Ltd'],
                'cost_price' => 8.00,
                'price' => 14.99,
                'quantity' => 100
            ],

            // Mobile Accessories
            [
                'name' => 'Anker PowerCore 26800',
                'description' => 'Portable Charger, 26800mAh',
                'sku' => 'ANKR-PWRBNK-001',
                'category_id' => $categoryIds['Mobile Accessories'],
                'supplier_id' => $supplierIds['Global Electronics'],
                'cost_price' => 40.00,
                'price' => 59.99,
                'quantity' => 35
            ],
            [
                'name' => 'OtterBox Defender Case',
                'description' => 'For iPhone 13 Pro, Black',
                'sku' => 'OTBX-CASE-001',
                'category_id' => $categoryIds['Mobile Accessories'],
                'supplier_id' => $supplierIds['Global Electronics'],
                'cost_price' => 30.00,
                'price' => 49.99,
                'quantity' => 40
            ],
            [
                'name' => 'Samsung Wireless Charger',
                'description' => '15W Fast Charging Pad',
                'sku' => 'SMSNG-CHRG-001',
                'category_id' => $categoryIds['Mobile Accessories'],
                'supplier_id' => $supplierIds['Global Electronics'],
                'cost_price' => 25.00,
                'price' => 39.99,
                'quantity' => 45
            ],

            // Printers & Scanners
            [
                'name' => 'HP LaserJet Pro M404dn',
                'description' => 'Monochrome Laser Printer, Duplex Printing',
                'sku' => 'HP-PRNT-001',
                'category_id' => $categoryIds['Printers & Scanners'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 220.00,
                'price' => 299.99,
                'quantity' => 10
            ],
            [
                'name' => 'Epson EcoTank ET-4760',
                'description' => 'All-in-One Supertank Printer',
                'sku' => 'EPSN-PRNT-001',
                'category_id' => $categoryIds['Printers & Scanners'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 350.00,
                'price' => 499.99,
                'quantity' => 8
            ],
            [
                'name' => 'Brother HL-L3270CDW',
                'description' => 'Color Laser Printer with Wireless',
                'sku' => 'BRTH-PRNT-001',
                'category_id' => $categoryIds['Printers & Scanners'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 200.00,
                'price' => 279.99,
                'quantity' => 12
            ],
            [
                'name' => 'Canon imageFORMULA R40',
                'description' => 'Office Document Scanner',
                'sku' => 'CANON-SCAN-001',
                'category_id' => $categoryIds['Printers & Scanners'],
                'supplier_id' => $supplierIds['TechWorld Distributors'],
                'cost_price' => 250.00,
                'price' => 349.99,
                'quantity' => 7
            ],
        ];

        // Create products and inventory
        foreach ($products as $productData) {
            $quantity = $productData['quantity'];
            unset($productData['quantity']);

            $product = Product::create($productData);

            // Create inventory record
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }
    }
}
