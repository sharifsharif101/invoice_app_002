<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Mail\SendInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use \PDF;




class InvoiceController extends Controller
{
    public function index()
    {
        $invoices =Invoice::orderBy("id","desc")->paginate(10);
        return view('frontend.index',compact('invoices'));
    }

    public function create()
    {
        return view('frontend.create');
    }

    public function store(Request $request)
    {
        $data['customer_name'] = $request->customer_name;
        $data['customer_email'] = $request->customer_email;
        $data['customer_mobile'] = $request->customer_mobile;
        $data['company_name'] = $request->company_name;
        $data['invoice_number'] = $request->invoice_number;
        $data['invoice_date'] = $request->invoice_date;
        $data['sub_total'] = $request->sub_total;
        $data['discount_type'] = $request->discount_type;
        $data['discount_value'] = $request->discount_value;
        $data['vat_value'] = $request->vat_value;
        $data['shipping'] = $request->shipping;
        $data['total_due'] = $request->total_due;

        $invoice = Invoice::create($data);

        $details_list = [];
        for ($i = 0; $i < count($request->product_name); $i++) {
            $details_list[$i]['product_name'] = $request->product_name[$i];
            $details_list[$i]['unit'] = $request->unit[$i];
            $details_list[$i]['quantity'] = $request->quantity[$i];
            $details_list[$i]['product_name'] = $request->product_name[$i];
            $details_list[$i]['unit_price'] = $request->unit_price[$i];
            $details_list[$i]['row_sub_total'] = $request->row_sub_total[$i];
        }

        $details = $invoice->details()->createMany($details_list);
        if($details){
            return redirect()->back()->with([
                'message'=> __('Frontend/frontend.created_successfully'),
                'alert-type'=>'success'
            ]);
        } else {
            return redirect()->back()->with([
                'message'=> __('Frontend/frontend.created_failed'),
                'alert-type'=> 'danger',
            ]);
        }
        
    }

    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('frontend.show',compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('frontend.edit',compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        // Debug the incoming request data
        $invoice = Invoice::whereId($id)->first();
        // Debug the fetched invoice data
        $data['customer_name'] = $request->customer_name;
        $data['customer_email'] = $request->customer_email;
        $data['customer_mobile'] = $request->customer_mobile;
        $data['company_name'] = $request->company_name;
        $data['invoice_number'] = $request->invoice_number;
        $data['invoice_date'] = $request->invoice_date;
        $data['sub_total'] = $request->sub_total;
        $data['discount_type'] = $request->discount_type;
        $data['discount_value'] = $request->discount_value;
        $data['vat_value'] = $request->vat_value;
        $data['shipping'] = $request->shipping;
        $data['total_due'] = $request->total_due;
    
        // Debug the data to be updated
        // dd($data);
    
        $invoice->update($data);
        $invoice->details()->delete();
    
        $details_list = [];
        // dd($request->product_name,$request->unit,$request->quantity,$request->unit_price,$request->row_sub_total);

        for ($i = 0; $i < count($request->product_name); $i++) {
            $details_list[$i]['product_name'] = $request->product_name[$i];
            $details_list[$i]['unit'] = $request->unit[$i];
            $details_list[$i]['quantity'] = $request->quantity[$i];
            $details_list[$i]['unit_price'] = $request->unit_price[$i];
            $details_list[$i]['row_sub_total'] = $request->row_sub_total[$i];
        }
    
        // Debug the details list before creating new records
       // dd($details_list);
    
        $details = $invoice->details()->createMany($details_list);
    
        if($details){
            return redirect()->back()->with([
                'message'=> __('Frontend/frontend.updated_successfully'),
                'alert-type'=>'success'
            ]);
        } else {
            return redirect()->back()->with([
                'message'=> __('Frontend/frontend.updated_failed'),
                'alert-type'=> 'danger',
            ]);
        }
    }
    
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($invoice) {
            $invoice->delete();
            return redirect()->route('invoice.index')->with([
                'message'=> __('Frontend/frontend.deleted_successfully'),
                'alert-type'=> 'success' 
            ]);
    
        } else {
            return redirect()->route('invoice.index')->with([
                'message'=> __('Frontend/frontend.deleted_failed'),
                'alert-type'=> 'danger' 
            ]);
        }
 
    }


    public function print($id){
        $invoice = Invoice::findOrFail($id);
        return view('frontend.print', compact('invoice'));
    }

    public function pdf($id)
    {
        // Fetch the invoice
        $invoice = Invoice::whereId($id)->first();
        // dd($invoice); // Debugging: Check if the invoice is retrieved correctly
    
        // If invoice is not found, return an error response
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }
    
        // Prepare invoice data
        $data['invoice_id'] = $invoice->id;
        $data['invoice_date'] = $invoice->invoice_date;
        $data['customer'] = [
            __('Frontend/frontend.customer_name') => $invoice->customer_name,
            __('Frontend/frontend.customer_mobile') => $invoice->customer_mobile,
            __('Frontend/frontend.customer_email') => $invoice->customer_email
        ];
        // dd($data); // Debugging: Check the data array after setting initial invoice and customer info
    
        // Fetch and prepare invoice items
        $items = [];
        $invoice_details = $invoice->details()->get();
        // dd($invoice_details); // Debugging: Check the invoice details fetched from the database
    
        foreach ($invoice_details as $item) {
            $items[] = [
                'product_name' => $item->product_name,
                'unit' => $item->unitText(),
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'row_sub_total' => $item->row_sub_total,
            ];
        }
        $data['items'] = $items;
        // dd($data); // Debugging: Check the data array after adding items
    
        // Set additional invoice data
        $data['invoice_number'] = $invoice->invoice_number;
        $data['created_at'] = $invoice->created_at->format('Y-m-d');
        $data['sub_total'] = $invoice->sub_total;
        $data['discount'] = $invoice->discountResult();
        $data['vat_value'] = $invoice->vat_value;
        $data['shipping'] = $invoice->shipping;
        $data['total_due'] = $invoice->total_due;
        // dd($data); // Debugging: Check the complete data array before generating the PDF
    
        // Generate PDF
        $pdf = PDF::loadView('frontend.pdf', $data);
        // dd($pdf); // Debugging: Check the PDF object
    
        // Add header to indicate inline content disposition
        if (Route::currentRouteName() == 'invoice.pdf') {
            return $pdf->download($invoice->invoice_number . '.pdf', ['Attachment' => false]);
        } else {
            $pdf->save(public_path('assets/invoices/') . $invoice->invoice_number . '.pdf');
            return $invoice->invoice_number . '.pdf';
        }
    }
    public function send_to_email($id){
        $invoice=Invoice::whereId($id)->first();
        $this->pdf($id);

        Mail::to($invoice->customer_email)->locale(config('app.locale'))->send(new SendInvoice($invoice));

        return redirect()->route('invoice.index')->with([
            'message' => __('Frontend/frontend.sent_successfully'),
            'alert-type' => 'success'
        ]);



    }
    



}
