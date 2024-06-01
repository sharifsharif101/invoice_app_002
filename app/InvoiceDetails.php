<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetails extends Model
{
  protected $guarded = [];

  public function invoice()
  {
    return $this->belongsTo(InvoiceDetails::class, "invoice_id", "id");
  }
  public function unitText()
  {
    if ($this->unit == 'piece') {
      $text = __('Frontend/frontend.piece');
    } else if ($this->unit == 'g') {
      $text = __('Frontend/frontend.gram');

    } elseif ($this->unit == 'kg') {
      $text = __('Frontend/frontend.kilo_gram');
    }
    return $text;
  }






















}
