<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Full currency name
            $table->string('code', 3); // Currency code (USD, EUR, etc.)
            $table->string('symbol'); // Currency symbol ($, €, etc.)
            $table->timestamps();
        });

        // Insert popular currencies
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$'],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€'],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£'],
            ['name' => 'Japanese Yen', 'code' => 'JPY', 'symbol' => '¥'],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => 'C$'],
            ['name' => 'Australian Dollar', 'code' => 'AUD', 'symbol' => 'A$'],
            ['name' => 'Swiss Franc', 'code' => 'CHF', 'symbol' => 'CHF'],
            ['name' => 'Chinese Yuan', 'code' => 'CNY', 'symbol' => '¥'],
            ['name' => 'Indian Rupee', 'code' => 'INR', 'symbol' => '₹'],
            ['name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => '₨'],
            ['name' => 'Nigerian Naira', 'code' => 'NGN', 'symbol' => '₦'],
            ['name' => 'South African Rand', 'code' => 'ZAR', 'symbol' => 'R'],
            ['name' => 'Brazilian Real', 'code' => 'BRL', 'symbol' => 'R$'],
            ['name' => 'Russian Ruble', 'code' => 'RUB', 'symbol' => '₽'],
            ['name' => 'South Korean Won', 'code' => 'KRW', 'symbol' => '₩'],
            ['name' => 'Singapore Dollar', 'code' => 'SGD', 'symbol' => 'S$'],
            ['name' => 'Hong Kong Dollar', 'code' => 'HKD', 'symbol' => 'HK$'],
            ['name' => 'Swedish Krona', 'code' => 'SEK', 'symbol' => 'kr'],
            ['name' => 'Norwegian Krone', 'code' => 'NOK', 'symbol' => 'kr'],
            ['name' => 'Danish Krone', 'code' => 'DKK', 'symbol' => 'kr'],
            ['name' => 'Polish Złoty', 'code' => 'PLN', 'symbol' => 'zł'],
            ['name' => 'Turkish Lira', 'code' => 'TRY', 'symbol' => '₺'],
            ['name' => 'Mexican Peso', 'code' => 'MXN', 'symbol' => '$'],
            ['name' => 'Argentine Peso', 'code' => 'ARS', 'symbol' => '$'],
            ['name' => 'Chilean Peso', 'code' => 'CLP', 'symbol' => '$'],
            ['name' => 'Colombian Peso', 'code' => 'COP', 'symbol' => '$'],
            ['name' => 'Peruvian Sol', 'code' => 'PEN', 'symbol' => 'S/'],
            ['name' => 'Uruguayan Peso', 'code' => 'UYU', 'symbol' => '$'],
            ['name' => 'Venezuelan Bolívar', 'code' => 'VES', 'symbol' => 'Bs'],
            ['name' => 'Egyptian Pound', 'code' => 'EGP', 'symbol' => '£'],
            ['name' => 'Moroccan Dirham', 'code' => 'MAD', 'symbol' => 'د.م.'],
            ['name' => 'Tunisian Dinar', 'code' => 'TND', 'symbol' => 'د.ت'],
            ['name' => 'Algerian Dinar', 'code' => 'DZD', 'symbol' => 'د.ج'],
            ['name' => 'Libyan Dinar', 'code' => 'LYD', 'symbol' => 'ل.د'],
            ['name' => 'Sudanese Pound', 'code' => 'SDG', 'symbol' => 'ج.س.'],
            ['name' => 'Ethiopian Birr', 'code' => 'ETB', 'symbol' => 'Br'],
            ['name' => 'Kenyan Shilling', 'code' => 'KES', 'symbol' => 'KSh'],
            ['name' => 'Ugandan Shilling', 'code' => 'UGX', 'symbol' => 'USh'],
            ['name' => 'Tanzanian Shilling', 'code' => 'TZS', 'symbol' => 'TSh'],
            ['name' => 'Ghanaian Cedi', 'code' => 'GHS', 'symbol' => '₵'],
            ['name' => 'Ivorian Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Senegalese Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Malian Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Burkina Faso Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Nigerian Naira', 'code' => 'NGN', 'symbol' => '₦'],
            ['name' => 'Cameroonian Franc', 'code' => 'XAF', 'symbol' => 'FCFA'],
            ['name' => 'Chadian Franc', 'code' => 'XAF', 'symbol' => 'FCFA'],
            ['name' => 'Central African Franc', 'code' => 'XAF', 'symbol' => 'FCFA'],
            ['name' => 'Gabonese Franc', 'code' => 'XAF', 'symbol' => 'FCFA'],
            ['name' => 'Equatorial Guinean Franc', 'code' => 'XAF', 'symbol' => 'FCFA'],
            ['name' => 'Congolese Franc', 'code' => 'CDF', 'symbol' => 'FC'],
            ['name' => 'Rwandan Franc', 'code' => 'RWF', 'symbol' => 'FRw'],
            ['name' => 'Burundian Franc', 'code' => 'BIF', 'symbol' => 'FBu'],
            ['name' => 'Malagasy Ariary', 'code' => 'MGA', 'symbol' => 'Ar'],
            ['name' => 'Mauritian Rupee', 'code' => 'MUR', 'symbol' => '₨'],
            ['name' => 'Seychellois Rupee', 'code' => 'SCR', 'symbol' => '₨'],
            ['name' => 'Comorian Franc', 'code' => 'KMF', 'symbol' => 'CF'],
            ['name' => 'Djiboutian Franc', 'code' => 'DJF', 'symbol' => 'Fdj'],
            ['name' => 'Somali Shilling', 'code' => 'SOS', 'symbol' => 'Sh.So.'],
            ['name' => 'Eritrean Nakfa', 'code' => 'ERN', 'symbol' => 'Nfk'],
            ['name' => 'South Sudanese Pound', 'code' => 'SSP', 'symbol' => 'SSP'],
            ['name' => 'Zambian Kwacha', 'code' => 'ZMW', 'symbol' => 'ZK'],
            ['name' => 'Malawian Kwacha', 'code' => 'MWK', 'symbol' => 'MK'],
            ['name' => 'Zimbabwean Dollar', 'code' => 'ZWL', 'symbol' => '$'],
            ['name' => 'Botswana Pula', 'code' => 'BWP', 'symbol' => 'P'],
            ['name' => 'Namibian Dollar', 'code' => 'NAD', 'symbol' => 'N$'],
            ['name' => 'Lesotho Loti', 'code' => 'LSL', 'symbol' => 'L'],
            ['name' => 'Eswatini Lilangeni', 'code' => 'SZL', 'symbol' => 'L'],
            ['name' => 'Mozambican Metical', 'code' => 'MZN', 'symbol' => 'MT'],
            ['name' => 'Angolan Kwanza', 'code' => 'AOA', 'symbol' => 'Kz'],
            ['name' => 'Cape Verdean Escudo', 'code' => 'CVE', 'symbol' => '$'],
            ['name' => 'Guinea-Bissau Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Guinean Franc', 'code' => 'GNF', 'symbol' => 'FG'],
            ['name' => 'Sierra Leonean Leone', 'code' => 'SLL', 'symbol' => 'Le'],
            ['name' => 'Liberian Dollar', 'code' => 'LRD', 'symbol' => '$'],
            ['name' => 'Gambian Dalasi', 'code' => 'GMD', 'symbol' => 'D'],
            ['name' => 'Mauritanian Ouguiya', 'code' => 'MRU', 'symbol' => 'UM'],
            ['name' => 'Togolese Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Beninese Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Nigerien Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Cabo Verdean Escudo', 'code' => 'CVE', 'symbol' => '$'],
            ['name' => 'São Tomé and Príncipe Dobra', 'code' => 'STN', 'symbol' => 'Db'],
            ['name' => 'Equatorial Guinean Franc', 'code' => 'XAF', 'symbol' => 'FCFA'],
            ['name' => 'Central African CFA Franc', 'code' => 'XAF', 'symbol' => 'FCFA'],
            ['name' => 'West African CFA Franc', 'code' => 'XOF', 'symbol' => 'CFA'],
            ['name' => 'Comorian Franc', 'code' => 'KMF', 'symbol' => 'CF'],
            ['name' => 'Djiboutian Franc', 'code' => 'DJF', 'symbol' => 'Fdj'],
            ['name' => 'Eritrean Nakfa', 'code' => 'ERN', 'symbol' => 'Nfk'],
            ['name' => 'Ethiopian Birr', 'code' => 'ETB', 'symbol' => 'Br'],
            ['name' => 'Kenyan Shilling', 'code' => 'KES', 'symbol' => 'KSh'],
            ['name' => 'Madagascar Ariary', 'code' => 'MGA', 'symbol' => 'Ar'],
            ['name' => 'Malawi Kwacha', 'code' => 'MWK', 'symbol' => 'MK'],
            ['name' => 'Mauritius Rupee', 'code' => 'MUR', 'symbol' => '₨'],
            ['name' => 'Mozambique Metical', 'code' => 'MZN', 'symbol' => 'MT'],
            ['name' => 'Rwanda Franc', 'code' => 'RWF', 'symbol' => 'FRw'],
            ['name' => 'Seychelles Rupee', 'code' => 'SCR', 'symbol' => '₨'],
            ['name' => 'Somalia Shilling', 'code' => 'SOS', 'symbol' => 'Sh.So.'],
            ['name' => 'South Sudan Pound', 'code' => 'SSP', 'symbol' => 'SSP'],
            ['name' => 'Tanzania Shilling', 'code' => 'TZS', 'symbol' => 'TSh'],
            ['name' => 'Uganda Shilling', 'code' => 'UGX', 'symbol' => 'USh'],
            ['name' => 'Zambia Kwacha', 'code' => 'ZMW', 'symbol' => 'ZK'],
            ['name' => 'Zimbabwe Dollar', 'code' => 'ZWL', 'symbol' => '$'],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->insert($currency);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
