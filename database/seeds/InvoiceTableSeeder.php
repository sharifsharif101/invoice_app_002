<?php

use Illuminate\Database\Seeder;
 use Carbon\Carbon;
 use App\Invoice;
use Faker\Factory;

class InvoiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('ar_SA');
        for ($i = 0; $i < 15; $i++) {
            $items = [
                [
                    'product_name' => 'طاولة كمبيوتر كبيرة',
                    'unit' => 'piece',
                    'quantity' => '2',
                    'unit_price' => '560',
                    'row_sub_total' => '1120',
                ],
                [
                    'product_name' => 'طاولة كمبيوتر صغيرة',
                    'unit' => 'piece',
                    'quantity' => '1',
                    'unit_price' => '220',
                    'row_sub_total' => '220',
                ],
                [
                    'product_name' => 'كمبيوتر محمول',
                    'unit' => 'piece',
                    'quantity' => '1',
                    'unit_price' => '4500',
                    'row_sub_total' => '4500',
                ]
            ];
   
            $data['customer_name'] = $faker->name('male');
            $data['customer_email'] = $faker->email;
            $data['customer_mobile'] = $this->generateNumber(rand(10,14));
            $data['company_name'] = $faker->company();
            $data['invoice_number'] =  $this->generateNumber(8);
        $data['invoice_date']= Carbon::now()->subDays(rand(1,20));
            $data['sub_total'] = '7101';
            $data['discount_type'] =  'fixed';
            $data['discount_value'] = '0';
            $data['vat_value'] = '355.05';
            $data['shipping'] = '100';
            $data['total_due'] = '7556.05';
            
            $invoice  = Invoice::create($data);
            $invoice->details()->createMany($items);

        }
    }

    public function generateNumber($strength = 14 ){
        $permitted_chars = '01234566789';

        $input_length = strlen($permitted_chars);

        $random_string = '';

        for ($i = 0; $i < $input_length; $i++) {
            $random_characters =$permitted_chars [mt_rand(0, $input_length -1)];
            $random_string .= $random_characters;
        }

        return $random_string;


    }






}
