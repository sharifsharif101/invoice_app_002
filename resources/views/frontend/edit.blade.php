@extends('layouts.app')
@section('style')
<link rel="stylesheet" href="{{ asset('frontend/css/pickadate/classic.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/css/pickadate/classic.date.css') }}">
@if (config('app.locale')=='ar')
<link rel="stylesheet" href="{{asset('frontend/css/pickadate/rtl.css')}}">
@endif

<style>
from.form label.error , label.error {
color: red;
font-style: italic;
}
</style>

@endsection
@section('content')
<div class="row justify-content-center">
<div class="col-12">
<div class="card ">
<div class="card-header d-flex">
<h2>{{ __('Frontend/frontend.invoice', ['invoice_number'=>$invoice->invoice_number]) }}</h2>
<a href="{{ route('invoice.index')}}" class="btn btn-primary ml-auto ">
<i class="fa fa-home "></i> {{ __('Frontend/frontend.back_to_invoice')}}
</a>
</div>
<div class="card-body">
<form action="{{ route('invoice.update' , $invoice->id) }}" method="POST" class="form">
    @csrf
    @method('PATCH')
{{-- //////////////////////////////// --}}


<div class="row">
<div class="col-4">
    <div class="form-group">
        <label for="customer_name">
            <i class="fas fa-user"></i> {{ __('Frontend/frontend.customer_name') }}
        </label>
        <input type="text" name="customer_name" 
        value="{{ old ('customer_name' , $invoice->customer_name)}}"
        id="customer_name" class="form-control rounded-pill">
        @error('customer_name')
            <span class="help-block text-danger font-weight-bold">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="col-4">
    <div class="form-group">
        <label for="customer_email">
            <i class="fas fa-envelope"></i> {{ __('Frontend/frontend.customer_email') }}
        </label>
        <input type="text" name="customer_email" 
        value="{{ old ('customer_email' , $invoice->customer_email)}}"
        id="customer_email" class="form-control rounded-pill">
        @error('customer_email')
            <span class="help-block text-danger font-weight-bold">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="col-4">
    <div class="form-group">
        <label for="customer_mobile">
            <i class="fas fa-phone"></i> {{ __('Frontend/frontend.customer_mobile') }}
        </label>
        <input type="text"
        value="{{ old ('customer_mobile' , $invoice->customer_mobile)}}"
        name="customer_mobile" id="customer_mobile" class="form-control rounded-pill">
        @error('customer_mobile')
            <span class="help-block text-danger font-weight-bold">{{ $message }}</span>
        @enderror
    </div>
</div>
</div>

<div class="row">
<div class="col-4">
    <div class="form-group">
        <label for="company_name">
            <i class="fas fa-building"></i> {{ __('Frontend/frontend.company_name') }}
        </label>
        <input type="text" 
        value="{{ old ('company_name' , $invoice->company_name)}}"
        name="company_name" id="company_name" class="form-control rounded-pill">
        @error('company_name')
            <span class="help-block text-danger font-weight-bold">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="col-4">
    <div class="form-group">
        <label for="invoice_number">
            <i class="fas fa-file-invoice"></i> {{ __('Frontend/frontend.invoice_number') }}
        </label>
        
        <input type="text" name="invoice_number" id="invoice_number"
        value="{{ old ('invoice_number' , $invoice->invoice_number)}}"
        class="form-control rounded-pill">
        @error('invoice_number')
            <span class="help-block text-danger font-weight-bold">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="col-4">
    <div class="form-group">
        <label for="invoice_date">
            <i class="fas fa-calendar-alt"></i> {{ __('Frontend/frontend.invoice_date') }}
        </label>
        <input type="text" 
        value="{{ old ('invoice_date' , $invoice->invoice_date)}}"
        name="invoice_date" class="form-control pickadate">
        @error('invoice_date')
            <span class="help-block text-danger font-weight-bold">{{ $message }}</span>
        @enderror
    </div>
</div>
</div>



    {{-- ///////////////////// --}}

    <div class="table-responsive">
        <table class="table" id="invoice_detailes">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ __('Frontend/frontend.product_name') }}</th>
                    <th>{{ __('Frontend/frontend.unit') }}</th>
                    <th>{{ __('Frontend/frontend.quantity') }}</th>
                    <th>{{ __('Frontend/frontend.unit_price') }}</th>
                    <th>{{ __('Frontend/frontend.subtotal') }}</th>
                </tr>
            </thead>
            

            {{-- ////////////////////////// --}}

            <tbody>
                 
    @foreach ($invoice->details as $itmes)
                <tr class="cloning_row" id="{{$loop->index}}">
                    <td>
                        @if ($loop->index == 0)
                            {{'#'}}
                        @else
                <button type="button" class="btn btn-danger btn-sm delegated-btn">
                    <i class="fa fa-minus"></i>
                </button>
                        @endif
                    </td>
                    <td>
                        <input type="text" name="product_name[{{$loop->index}}]" 
                        value="{{ old ('product_name' , $itmes->product_name)}}"
                        id="product_name"
                            class="product_name form-control ">
                        @error('product_name')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <select name="unit[{{$loop->index}}]" id="unit" class="unit form-control ">
                            <option></option>
                            <option value="piece" {{$itmes->unit == 'piece' ? 'selected' : '' }}>Piece</option>
                            <option  
                            value="g" {{$itmes->unit == 'g' ? 'selected' : '' }}
                            >Gram</option>
                            <option 
                            value="kg" {{$itmes->unit == 'kg' ? 'selected' : '' }}
                            >Kilo Gram</option>
                        </select>
                        @error('unit')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <input type="number" name="quantity[{{$loop->index}}]" step="0.01" 
                        value="{{ old ('quantity' , $itmes->quantity)}}"
                        id="quantity"
                            class="quantity form-control">
                        @error('quantity')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <input type="number" name="unit_price[{{$loop->index}}]" step="0.01" 
                        value="{{ old ('unit_price' , $itmes->unit_price)}}"
                        id="unit_price"
                            class="unit_price form-control">
                        @error('unit_price')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <input type="number" step="0.01" name="row_sub_total[{{ $loop->index }}]" id="row_sub_total" value="{{ old('row_sub_total', $itmes->row_sub_total) }}" class="row_sub_total form-control" readonly="readonly">
                        @error('row_sub_total')<span class="help-block text-danger">{{ $message }}</span>@enderror
                    </td>
                </tr>

                @endforeach
            </tbody>

            {{-- ////////////////////// --}}
            <tfoot>
                <tr>
                    <td colspan="6">
                        <button type="button" class="btn_add btn btn-primary">{{ __('Frontend/frontend.add_another_product') }}</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">{{ __('Frontend/frontend.sub_total') }}</td>
                    <td>
                        <input type="number" name="sub_total" 
                        value="{{ old ('sub_total' , $invoice->sub_total)}}" 
                        id="sub_total" class="sub_total form-control" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">{{ __('Frontend/frontend.discount') }}</td>
                    <td>
                        <div class="input-group mb-3">
                            <select name="discount_type" id="discount_type" 
                             class="discount_type custom-select">
                                <option value="fixed" 
                                {{$invoice->discount_type == 'fixed' ? 'selected' : '' }}
                                >{{ __('Frontend/frontend.fixed') }}</option>
                                <option value="percentage"
                                {{$invoice->discount_type == 'percentage' ? 'selected' : '' }}

                                >{{ __('Frontend/frontend.percentage') }}</option>
                            </select>
                            <div class="input-group-append">
                                <input type="number" step="0.01" name="discount_value"
                                value="{{ old ('discount_value' , $invoice->discount_value)}}" 
                                id="discount_value" class="discount_value form-control">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">{{ __('Frontend/frontend.vat') }} </td>
                    <td>
                        <input type="number" step="0.01" name="vat_value"
                        value="{{ old ('vat_value' , $invoice->vat_value)}}" 
                        id="vat_value" class="vat_value form-control" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">{{ __('Frontend/frontend.shipping') }}</td>
                    <td>
                        <input type="number" step="0.01" name="shipping" 
                        value="{{ old ('shipping' , $invoice->shipping)}}" 
                        id="shipping" class="shipping form-control">
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">{{ __('Frontend/frontend.total_due') }}</td>
                    <td>
                        <input type="number" step="0.01" name="total_due" 
                        value="{{ old ('total_due' , $invoice->total_due)}}" 
                        id="total_due" class="total_due form-control" readonly="readonly">
                    </td>
                </tr>
            </tfoot>
            

        </table>
    </div>
<div class="text-right pt-3">
<button type="submit" name="save" class="btn btn-primary ">{{ __('Frontend/frontend.update') }}</button>
</div>

</form>
</div>
</div>
</div>

</div>
@endsection
@section('script')

<script src="{{ asset('frontend/js/form_validation/jquery.form.js') }}"></script>
<script src="{{ asset('frontend/js/form_validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('frontend/js/form_validation/additional-methods.min.js') }}"></script>
<script src="{{ asset('frontend/js/pickadate/picker.js') }}"></script>
<script src="{{ asset('frontend/js/pickadate/picker.date.js') }}"></script>
@if (config('app.locale') == 'ar')
<script src="{{ asset('frontend/js/form_validation/messages_ar.js') }}"></script>
<script src="{{ asset('frontend/js/pickadate/ar.js') }}"></script>
@endif

<script src="{{ asset('frontend/js/custom.js') }}" > </script>

@endsection
