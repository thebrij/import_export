<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExporterBill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipping_bill_no', 'shipping_bill_date', 'iec', 'exporter', 'exporter_address_n_city', 'city', 'pin', 'state', 'contact_no', 'e_mail_id', 'consinee', 'consinee_address', 'port_code', 'foreign_port', 'foreign_country', 'hs_code', 'chapter', 'product_descripition', 'quantity', 'unit_quantity', 'item_rate_in_fc', 'currency', 'total_value_in_usd', 'unit_rate_in_usd_exchange', 'exchange_rate_usd', 'total_value_in_usd_exchange', 'unit_value_in_inr', 'fob_in_inr', 'invoice_serial_number', 'invoice_number', 'item_number', 'drawback', 'month', 'year', 'mode', 'indian_port', 'cush', 'file_name', 'created_by', 'updated_by', 'created_at', 'updated_at',
    ];
}
