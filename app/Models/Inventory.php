<?php
// app/Models/Inventory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = ['item_name','item_price', 'stock', 'last_updated_by'];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}