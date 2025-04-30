<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'cost_price',
        'category_id',
        'supplier_id',
        'image',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier that owns the product.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the inventory for the product.
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * Get the purchase order items for the product.
     */
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate HTML barcode for the product
     *
     * @return string
     */
    public function getBarcode()
    {
        $generator = new BarcodeGeneratorHTML();
        return $generator->getBarcode($this->sku, $generator::TYPE_CODE_128, 2, 50);
    }

    /**
     * Generate PNG barcode for the product
     *
     * @return string
     */
    public function getBarcodePNG()
    {
        $generator = new BarcodeGeneratorPNG();
        return 'data:image/png;base64,' . base64_encode($generator->getBarcode($this->sku, $generator::TYPE_CODE_128, 2, 50));
    }

    /**
     * Generate QR code for the product
     *
     * @return string
     */
    public function getQRCode()
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($this->sku);
    }
}
