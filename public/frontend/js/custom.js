$(document).ready(function() {

$('.pickadate').pickadate({
format: 'yyyy-mm-dd',
selectMonth: true,
selectYear: true,
clear: 'Clear',
close: 'Ok',
closeOnSelect: true
});

$('#invoice_detailes').on('keyup blur', '.quantity, .unit_price', function() {
let $row = $(this).closest('tr');
let quantity = parseFloat($row.find('.quantity').val()) || 0;
let unit_price = parseFloat($row.find('.unit_price').val()) || 0;

$row.find('.row_sub_total').val((quantity * unit_price).toFixed(2));
updateTotals();
});

$('#invoice_detailes').on('keyup blur', '.discount_value, .discount_type, .shipping', function() {
updateTotals();
});

function updateTotals() {
$('#sub_total').val(sumTotal('.row_sub_total'));
$('#vat_value').val(calculateVat());
$('#total_due').val(sumDueTotal());
}

function sumTotal(selector) {
let sum = 0;
$(selector).each(function() {
let value = parseFloat($(this).val()) || 0;
sum += value;
});
return sum.toFixed(2);
}

function calculateVat() {
let subTotal = parseFloat($('#sub_total').val()) || 0;
let discountType = $('.discount_type').val();
let discountValue = parseFloat($('.discount_value').val()) || 0;

let discount = 0;
if (discountValue !== 0) {
if (discountType === 'percentage') {
discount = subTotal * (discountValue / 100);
} else {
discount = discountValue;
}
}

let vat = (subTotal - discount) * 0.05;
return vat.toFixed(2);
}

function sumDueTotal() {
let subTotal = parseFloat($('#sub_total').val()) || 0;
let discountType = $('.discount_type').val();
let discountValue = parseFloat($('.discount_value').val()) || 0;

let discount = 0;
if (discountValue !== 0) {
if (discountType === 'percentage') {
discount = subTotal * (discountValue / 100);
} else {
discount = discountValue;
}
}

let vat = parseFloat($('#vat_value').val()) || 0;
let shipping = parseFloat($('.shipping').val()) || 0;

let totalDue = subTotal - discount + vat + shipping;
return totalDue.toFixed(2);
}

$(document).on('click', '.btn_add', function() {
let trCount = $('#invoice_detailes').find('tr.cloning_row:last').length;
let numberIncr = trCount > 0 ? parseInt($('#invoice_detailes').find('tr.cloning_row:last').attr('id')) + 1 : 0;

$('#invoice_detailes').find('tbody').append(`
<tr class="cloning_row" id="${numberIncr}">
<td><button type="button" class="btn btn-danger btn-sm delegated-btn"><i class="fa fa-minus"></i></button></td>
<td><input type="text" name="product_name[${numberIncr}]" class="product_name form-control"></td>
<td>
    <select name="unit[${numberIncr}]" class="unit form-control">
        <option></option>
        <option value="piece">Piece</option>
        <option value="g">Gram</option>
        <option value="kg">Kilo Gram</option>
    </select>
</td>
<td><input type="number" name="quantity[${numberIncr}]" step="0.01" class="quantity form-control"></td>
<td><input type="number" name="unit_price[${numberIncr}]" step="0.01" class="unit_price form-control"></td>
<td><input type="number" step="0.01" name="row_sub_total[${numberIncr}]" class="row_sub_total form-control" readonly="readonly"></td>
</tr>
`);
});

$(document).on('click', '.delegated-btn', function(e) {
e.preventDefault();
$(this).closest('tr').remove();
updateTotals();
});

$('form').on('submit',function(e){
$('input.product_name').each(function (){
$(this).rules("add", {required: true});
});
$('input.unit').each(function (){
$(this).rules("add", {required:true});
});
$('input.quantity').each(function (){
$(this).rules("add", {required:true});
});
$('select.unit').each(function () { $(this).rules("add", { required: true }); });
$('input.unit_price').each(function (){
$(this).rules("add", {required:true});
});
$('input.row_sub_total').each(function (){
$(this).rules("add", {required:true});
});
e.preventDefault();
});

$('form').validate({
rules:{
'customer_name':{required:true},
'customer_email':{required:true},
'customer_mobile':{required:true ,digits:true,minlength:10,maxlength:14},
'company_name':{required:true},
'invoice_number':{required:true},
'invoice_date':{required:true}
},
submitHandler :function (form){
form.submit();
}
})



});
 