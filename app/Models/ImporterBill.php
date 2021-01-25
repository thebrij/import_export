<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImporterBill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bill_of_entry_no', 'bill_of_entry_date', 'importer_id', 'importer_name', 'importer_address', 'importer_city_state', 'pin_code', 'city', 'state', 'contact_no', 'email', 'supplier', 'supplier_address', 'foreign_port', 'origin_country', 'hs_code', 'chapter', 'product_discription', 'quantity', 'unit_quantity', 'unit_value_as_per_invoice', 'invoice_currency', 'total_value_in_fc', 'unit_rate_in_usd', 'exchange_rate', 'total_value_usd_exchange', 'unit_price_in_inr', 'assess_value_in_inr', 'duty', 'cha_number', 'cha_name', 'invoice_no', 'item_number', 'be_type', 'a_group', 'indian_port', 'cush', 'file_name', 'created_by', 'updated_by', 'created_at', 'updated_at',
    ];


}

