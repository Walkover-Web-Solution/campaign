<?php

namespace Database\Seeders;

use App\Models\Filter;
use Illuminate\Database\Seeder;

class FilterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Filter::truncate();
        $countriesJson = [
            [
                "Country" => "United States and Canada ",
                "filterValue" => "US-CA",
                "DialingCode" => "1",
            ],
            [
                "Country" => "Afghanistan ",
                "filterValue" => "AF",
                "DialingCode" => "93",
            ],
            [
                "Country" => "Albania ",
                "filterValue" => "AL",
                "DialingCode" => "355",
            ],
            [
                "Country" => "Algeria ",
                "filterValue" => "DZ",
                "DialingCode" => "213",
            ],
            [
                "Country" => "American Samoa",
                "filterValue" => "AS",
                "DialingCode" => "1684",
            ],
            [
                "Country" => "Andorra",
                "filterValue" => "AD",
                "DialingCode" => "376",
            ],
            [
                "Country" => "Angola",
                "filterValue" => "AO",
                "DialingCode" => "244",
            ],
            [
                "Country" => "Anguilla ",
                "filterValue" => "AI",
                "DialingCode" => "1264",
            ],
            [
                "Country" => "Antarctica Norfolk Island",
                "filterValue" => "AQ-NF",
                "DialingCode" => "672",
            ],
            [
                "Country" => "Antigua and Barbuda",
                "filterValue" => "AG",
                "DialingCode" => "1268",
            ],
            [
                "Country" => "Argentina ",
                "filterValue" => "AR",
                "DialingCode" => "54",
            ],
            [
                "Country" => "Armenia",
                "filterValue" => "AM",
                "DialingCode" => "374",
            ],
            [
                "Country" => "Aruba",
                "filterValue" => "AW",
                "DialingCode" => "297",
            ],
            [
                "Country" => "Australia",
                "filterValue" => "AU",
                "DialingCode" => "61",
            ],
            [
                "Country" => "Austria",
                "filterValue" => "AT",
                "DialingCode" => "43",
            ],
            [
                "Country" => "Azerbaijan or Azerbaidjan",
                "filterValue" => "AZ",
                "DialingCode" => "994",
            ],
            [
                "Country" => "Bahamas",
                "filterValue" => "BS",
                "DialingCode" => "1242",
            ],
            [
                "Country" => "Bahrain",
                "filterValue" => "BH",
                "DialingCode" => "973",
            ],
            [
                "Country" => "Bangladesh",
                "filterValue" => "BD",
                "DialingCode" => "880",
            ],
            [
                "Country" => "Barbados ",
                "filterValue" => "BB",
                "DialingCode" => "1246",
            ],
            [
                "Country" => "Belarus",
                "filterValue" => "BY",
                "DialingCode" => "375",
            ],
            [
                "Country" => "Belgium ",
                "filterValue" => "BE",
                "DialingCode" => "32",
            ],
            [
                "Country" => "Belize ",
                "filterValue" => "BZ",
                "DialingCode" => "501",
            ],
            [
                "Country" => "Benin",
                "filterValue" => "BJ",
                "DialingCode" => "229",
            ],
            [
                "Country" => "Bermuda ",
                "filterValue" => "BM",
                "DialingCode" => "1441",
            ],
            [
                "Country" => "Bhutan",
                "filterValue" => "BT",
                "DialingCode" => "975",
            ],
            [
                "Country" => "Bolivia ",
                "filterValue" => "BO",
                "DialingCode" => "591",
            ],
            [
                "Country" => "Bosnia and Herzegovina ",
                "filterValue" => "BA",
                "DialingCode" => "387",
            ],
            [
                "Country" => "Botswana",
                "filterValue" => "BW",
                "DialingCode" => "267",
            ],
            [
                "Country" => "Brazil ",
                "filterValue" => "BR",
                "DialingCode" => "55",
            ],
            [
                "Country" => "Brunei ",
                "filterValue" => "BN",
                "DialingCode" => "673",
            ],
            [
                "Country" => "Bulgaria ",
                "filterValue" => "BG",
                "DialingCode" => "359",
            ],
            [
                "Country" => "Burkina Faso ",
                "filterValue" => "BF",
                "DialingCode" => "226",
            ],
            [
                "Country" => "Burundi ",
                "filterValue" => "BI",
                "DialingCode" => "257",
            ],
            [
                "Country" => "Cambodia",
                "filterValue" => "KH",
                "DialingCode" => "855",
            ],
            [
                "Country" => "Cameroon ",
                "filterValue" => "CM",
                "DialingCode" => "237",
            ],
            [
                "Country" => "Cape Verde ",
                "filterValue" => "CV",
                "DialingCode" => "238",
            ],
            [
                "Country" => "Cayman Islands ",
                "filterValue" => "KY",
                "DialingCode" => "1345",
            ],
            [
                "Country" => "Central African Republic ",
                "filterValue" => "CF",
                "DialingCode" => "236",
            ],
            [
                "Country" => "Chad ",
                "filterValue" => "TD",
                "DialingCode" => "235",
            ],
            [
                "Country" => "Chile ",
                "filterValue" => "CL",
                "DialingCode" => "56",
            ],
            [
                "Country" => "China ",
                "filterValue" => "CN",
                "DialingCode" => "86",
            ],
            [
                "Country" => "Christmas Island ",
                "filterValue" => "CX",
                "DialingCode" => "53",
            ],
            [
                "Country" => "Cocos (Keeling) Islands ",
                "filterValue" => "CC",
                "DialingCode" => "61",
            ],
            [
                "Country" => "Colombia ",
                "filterValue" => "CO",
                "DialingCode" => "57",
            ],
            [
                "Country" => "Comoros and Mayotte",
                "filterValue" => "KM-YT",
                "DialingCode" => "269",
            ],
            [
                "Country" => "Congo, Democratic Republic of the",
                "filterValue" => "CD",
                "DialingCode" => "243",
            ],
            [
                "Country" => "Congo, Republic of the",
                "filterValue" => "CG",
                "DialingCode" => "242",
            ],
            [
                "Country" => "Cook Islands",
                "filterValue" => "CK",
                "DialingCode" => "682",
            ],
            [
                "Country" => "Costa Rica ",
                "filterValue" => "CR",
                "DialingCode" => "506",
            ],
            [
                "Country" => "Cote D\'Ivoire ",
                "filterValue" => "CI",
                "DialingCode" => "225",
            ],
            [
                "Country" => "Croatia ",
                "filterValue" => "HR",
                "DialingCode" => "385",
            ],
            [
                "Country" => "Cuba ",
                "filterValue" => "CU",
                "DialingCode" => "53",
            ],
            [
                "Country" => "Cyprus ",
                "filterValue" => "CY",
                "DialingCode" => "357",
            ],
            [
                "Country" => "Czech Republic",
                "filterValue" => "CZ",
                "DialingCode" => "420",
            ],
            [
                "Country" => "Denmark ",
                "filterValue" => "DK",
                "DialingCode" => "45",
            ],
            [
                "Country" => "Djibouti",
                "filterValue" => "DJ",
                "DialingCode" => "253",
            ],
            [
                "Country" => "Dominica ",
                "filterValue" => "DM",
                "DialingCode" => "1767",
            ],
            [
                "Country" => "Dominican Republic ",
                "filterValue" => "DO",
                "DialingCode" => "1829",
            ],
            [
                "Country" => "Dominican Republic ",
                "filterValue" => "DO",
                "DialingCode" => "1809",
            ],
            [
                "Country" => "East Timor ",
                "filterValue" => "TP",
                "DialingCode" => "670",
            ],
            [
                "Country" => "Ecuador ",
                "filterValue" => "EC",
                "DialingCode" => "593 ",
            ],
            [
                "Country" => "Egypt ",
                "filterValue" => "EG",
                "DialingCode" => "20",
            ],
            [
                "Country" => "El Salvador ",
                "filterValue" => "SV",
                "DialingCode" => "503",
            ],
            [
                "Country" => "Equatorial Guinea ",
                "filterValue" => "GQ",
                "DialingCode" => "240",
            ],
            [
                "Country" => "Eritrea ",
                "filterValue" => "ER",
                "DialingCode" => "291",
            ],
            [
                "Country" => "Estonia",
                "filterValue" => "EE",
                "DialingCode" => "372",
            ],
            [
                "Country" => "Ethiopia ",
                "filterValue" => "ET",
                "DialingCode" => "251",
            ],
            [
                "Country" => "Falkland Islands ",
                "filterValue" => "FK",
                "DialingCode" => "500",
            ],
            [
                "Country" => "Faroe Islands ",
                "filterValue" => "FO",
                "DialingCode" => "298",
            ],
            [
                "Country" => "Fiji ",
                "filterValue" => "FJ",
                "DialingCode" => "679",
            ],
            [
                "Country" => "Finland ",
                "filterValue" => "FI",
                "DialingCode" => "358",
            ],
            [
                "Country" => "France ",
                "filterValue" => "FR",
                "DialingCode" => "33",
            ],
            [
                "Country" => "French Guiana",
                "filterValue" => "GF",
                "DialingCode" => "594",
            ],
            [
                "Country" => "French Polynesia",
                "filterValue" => "PF",
                "DialingCode" => "689",
            ],
            [
                "Country" => "Gabon ",
                "filterValue" => "GA",
                "DialingCode" => "241",
            ],
            [
                "Country" => "Gambia, The ",
                "filterValue" => "GM",
                "DialingCode" => "220",
            ],
            [
                "Country" => "Georgia",
                "filterValue" => "GE",
                "DialingCode" => "995",
            ],
            [
                "Country" => "Germany ",
                "filterValue" => "DE",
                "DialingCode" => "49",
            ],
            [
                "Country" => "Ghana ",
                "filterValue" => "GH",
                "DialingCode" => "233",
            ],
            [
                "Country" => "Gibraltar ",
                "filterValue" => "GI",
                "DialingCode" => "350",
            ],
            [
                "Country" => "Greece ",
                "filterValue" => "GR",
                "DialingCode" => "30",
            ],
            [
                "Country" => "Greenland ",
                "filterValue" => "GL",
                "DialingCode" => "299",
            ],
            [
                "Country" => "Grenada ",
                "filterValue" => "GD",
                "DialingCode" => "1473",
            ],
            [
                "Country" => "Guadeloupe",
                "filterValue" => "GP",
                "DialingCode" => "590",
            ],
            [
                "Country" => "Guam",
                "filterValue" => "GU",
                "DialingCode" => "1671",
            ],
            [
                "Country" => "Guatemala ",
                "filterValue" => "GT",
                "DialingCode" => "502",
            ],
            [
                "Country" => "Guinea",
                "filterValue" => "GN",
                "DialingCode" => "224",
            ],
            [
                "Country" => "Guinea-Bissau ",
                "filterValue" => "GW",
                "DialingCode" => "245",
            ],
            [
                "Country" => "Guyana ",
                "filterValue" => "GY",
                "DialingCode" => "592",
            ],
            [
                "Country" => "Haiti ",
                "filterValue" => "HT",
                "DialingCode" => "509",
            ],
            [
                "Country" => "Honduras ",
                "filterValue" => "HN",
                "DialingCode" => "504",
            ],
            [
                "Country" => "Hong Kong ",
                "filterValue" => "HK",
                "DialingCode" => "852",
            ],
            [
                "Country" => "Hungary ",
                "filterValue" => "HU",
                "DialingCode" => "36",
            ],
            [
                "Country" => "Iceland ",
                "filterValue" => "IS",
                "DialingCode" => "354",
            ],
            [
                "Country" => "India ",
                "filterValue" => "IN",
                "DialingCode" => "91",
            ],
            [
                "Country" => "Indonesia",
                "filterValue" => "ID",
                "DialingCode" => "62",
            ],
            [
                "Country" => "Iran",
                "filterValue" => "IR",
                "DialingCode" => "98",
            ],
            [
                "Country" => "Iraq ",
                "filterValue" => "IQ",
                "DialingCode" => "964",
            ],
            [
                "Country" => "Ireland ",
                "filterValue" => "IE",
                "DialingCode" => "353",
            ],
            [
                "Country" => "Israel ",
                "filterValue" => "IL",
                "DialingCode" => "972",
            ],
            [
                "Country" => "Italy ",
                "filterValue" => "IT",
                "DialingCode" => "39",
            ],
            [
                "Country" => "Jamaica ",
                "filterValue" => "JM",
                "DialingCode" => "1876",
            ],
            [
                "Country" => "Japan ",
                "filterValue" => "JP",
                "DialingCode" => "81",
            ],
            [
                "Country" => "Jordan",
                "filterValue" => "JO",
                "DialingCode" => "962",
            ],
            [
                "Country" => "Kenya ",
                "filterValue" => "KE",
                "DialingCode" => "254",
            ],
            [
                "Country" => "Kiribati",
                "filterValue" => "KI",
                "DialingCode" => "686",
            ],
            [
                "Country" => "North Korea",
                "filterValue" => "KP",
                "DialingCode" => "850",
            ],
            [
                "Country" => "South Korea ",
                "filterValue" => "KR",
                "DialingCode" => "82",
            ],
            [
                "Country" => "Kuwait ",
                "filterValue" => "KW",
                "DialingCode" => "965",
            ],
            [
                "Country" => "Kyrgyzstan",
                "filterValue" => "KG",
                "DialingCode" => "996",
            ],
            [
                "Country" => "Laos",
                "filterValue" => "LA",
                "DialingCode" => "856",
            ],
            [
                "Country" => "Latvia",
                "filterValue" => "LV",
                "DialingCode" => "371",
            ],
            [
                "Country" => "Lebanon ",
                "filterValue" => "LB",
                "DialingCode" => "961",
            ],
            [
                "Country" => "Lesotho",
                "filterValue" => "LS",
                "DialingCode" => "266",
            ],
            [
                "Country" => "Liberia ",
                "filterValue" => "LR",
                "DialingCode" => "231",
            ],
            [
                "Country" => "Libya ",
                "filterValue" => "LY",
                "DialingCode" => "218",
            ],
            [
                "Country" => "Liechtenstein ",
                "filterValue" => "LI",
                "DialingCode" => "423",
            ],
            [
                "Country" => "Lithuania ",
                "filterValue" => "LT",
                "DialingCode" => "370",
            ],
            [
                "Country" => "Luxembourg ",
                "filterValue" => "LU",
                "DialingCode" => "352",
            ],
            [
                "Country" => "Macau ",
                "filterValue" => "MO",
                "DialingCode" => "853",
            ],
            [
                "Country" => "Macedonia",
                "filterValue" => "MK",
                "DialingCode" => "389",
            ],
            [
                "Country" => "Madagascar ",
                "filterValue" => "MG",
                "DialingCode" => "261",
            ],
            [
                "Country" => "Malawi ",
                "filterValue" => "MW",
                "DialingCode" => "265",
            ],
            [
                "Country" => "Malaysia ",
                "filterValue" => "MY",
                "DialingCode" => "60",
            ],
            [
                "Country" => "Maldives ",
                "filterValue" => "MV",
                "DialingCode" => "960",
            ],
            [
                "Country" => "Mali ",
                "filterValue" => "ML",
                "DialingCode" => "223",
            ],
            [
                "Country" => "Malta ",
                "filterValue" => "MT",
                "DialingCode" => "356",
            ],
            [
                "Country" => "Marshall Islands ",
                "filterValue" => "MH",
                "DialingCode" => "692",
            ],
            [
                "Country" => "Martinique",
                "filterValue" => "MQ",
                "DialingCode" => "596",
            ],
            [
                "Country" => "Mauritania ",
                "filterValue" => "MR",
                "DialingCode" => "222",
            ],
            [
                "Country" => "Mauritius ",
                "filterValue" => "MU",
                "DialingCode" => "230",
            ],
            [
                "Country" => "Mexico ",
                "filterValue" => "MX",
                "DialingCode" => "52",
            ],
            [
                "Country" => "Micronesia",
                "filterValue" => "FM",
                "DialingCode" => "691",
            ],
            [
                "Country" => "Moldova",
                "filterValue" => "MD",
                "DialingCode" => "373",
            ],
            [
                "Country" => "Monaco",
                "filterValue" => "MC",
                "DialingCode" => "377",
            ],
            [
                "Country" => "Mongolia",
                "filterValue" => "MN",
                "DialingCode" => "976",
            ],
            [
                "Country" => "Montserrat ",
                "filterValue" => "MS",
                "DialingCode" => "1664",
            ],
            [
                "Country" => "Morocco ",
                "filterValue" => "MA",
                "DialingCode" => "212",
            ],
            [
                "Country" => "Mozambique ",
                "filterValue" => "MZ",
                "DialingCode" => "258",
            ],
            [
                "Country" => "Myanmar",
                "filterValue" => "MM",
                "DialingCode" => "95",
            ],
            [
                "Country" => "Namibia ",
                "filterValue" => "NA",
                "DialingCode" => "264",
            ],
            [
                "Country" => "Nauru ",
                "filterValue" => "NR",
                "DialingCode" => "674",
            ],
            [
                "Country" => "Nepal ",
                "filterValue" => "NP",
                "DialingCode" => "977",
            ],
            [
                "Country" => "Netherlands ",
                "filterValue" => "NL",
                "DialingCode" => "31",
            ],
            [
                "Country" => "Netherlands Antilles ",
                "filterValue" => "AN",
                "DialingCode" => "599",
            ],
            [
                "Country" => "New Caledonia ",
                "filterValue" => "NC",
                "DialingCode" => "687",
            ],
            [
                "Country" => "New Zealand ",
                "filterValue" => "NZ",
                "DialingCode" => "64",
            ],
            [
                "Country" => "Nicaragua ",
                "filterValue" => "NI",
                "DialingCode" => "505",
            ],
            [
                "Country" => "Niger ",
                "filterValue" => "NE",
                "DialingCode" => "227",
            ],
            [
                "Country" => "Nigeria ",
                "filterValue" => "NG",
                "DialingCode" => "234",
            ],
            [
                "Country" => "Niue",
                "filterValue" => "NU",
                "DialingCode" => "683",
            ],
            [
                "Country" => "Northern Mariana Islands ",
                "filterValue" => "MP",
                "DialingCode" => "1670",
            ],
            [
                "Country" => "Norway ",
                "filterValue" => "NO",
                "DialingCode" => "47",
            ],
            [
                "Country" => "Oman",
                "filterValue" => "OM",
                "DialingCode" => "968",
            ],
            [
                "Country" => "Pakistan",
                "filterValue" => "PK",
                "DialingCode" => "92",
            ],
            [
                "Country" => "Palau ",
                "filterValue" => "PW",
                "DialingCode" => "680",
            ],
            [
                "Country" => "Palestinian State",
                "filterValue" => "PS",
                "DialingCode" => "970",
            ],
            [
                "Country" => "Panama ",
                "filterValue" => "PA",
                "DialingCode" => "507",
            ],
            [
                "Country" => "Papua New Guinea ",
                "filterValue" => "PG",
                "DialingCode" => "675",
            ],
            [
                "Country" => "Paraguay ",
                "filterValue" => "PY",
                "DialingCode" => "595",
            ],
            [
                "Country" => "Peru ",
                "filterValue" => "PE",
                "DialingCode" => "51",
            ],
            [
                "Country" => "Philippines ",
                "filterValue" => "PH",
                "DialingCode" => "63",
            ],
            [
                "Country" => "Poland ",
                "filterValue" => "PL",
                "DialingCode" => "48",
            ],
            [
                "Country" => "Portugal ",
                "filterValue" => "PT",
                "DialingCode" => "351",
            ],
            [
                "Country" => "Puerto Rico ",
                "filterValue" => "PR",
                "DialingCode" => "1939",
            ],
            [
                "Country" => "Puerto Rico ",
                "filterValue" => "PR",
                "DialingCode" => "1787",
            ],
            [
                "Country" => "Qata",
                "filterValue" => "QA",
                "DialingCode" => "974 ",
            ],
            [
                "Country" => "Reunion ",
                "filterValue" => "RE",
                "DialingCode" => "262",
            ],
            [
                "Country" => "Romania ",
                "filterValue" => "RO",
                "DialingCode" => "40",
            ],
            [
                "Country" => "Russian Federation and Kazakstan ",
                "filterValue" => "RU-KZ",
                "DialingCode" => "7",
            ],
            [
                "Country" => "Rwanda ",
                "filterValue" => "RW",
                "DialingCode" => "250",
            ],
            [
                "Country" => "Saint Helena ",
                "filterValue" => "SH",
                "DialingCode" => "290",
            ],
            [
                "Country" => "Saint Kitts and Nevis ",
                "filterValue" => "KN",
                "DialingCode" => "1869",
            ],
            [
                "Country" => "Saint Lucia ",
                "filterValue" => "LC",
                "DialingCode" => "1758",
            ],
            [
                "Country" => "Saint Pierre and Miquelon ",
                "filterValue" => "PM",
                "DialingCode" => "508",
            ],
            [
                "Country" => "Saint Vincent and the Grenadines ",
                "filterValue" => "VC",
                "DialingCode" => "1784",
            ],
            [
                "Country" => "Samoa ",
                "filterValue" => "WS",
                "DialingCode" => "685",
            ],
            [
                "Country" => "San Marino ",
                "filterValue" => "SM",
                "DialingCode" => "378",
            ],
            [
                "Country" => "Sao Tome and Principe ",
                "filterValue" => "ST",
                "DialingCode" => "239",
            ],
            [
                "Country" => "Saudi Arabia ",
                "filterValue" => "SA",
                "DialingCode" => "966",
            ],
            [
                "Country" => "Senegal ",
                "filterValue" => "SN",
                "DialingCode" => "221",
            ],
            [
                "Country" => "Seychelles ",
                "filterValue" => "SC",
                "DialingCode" => "248",
            ],
            [
                "Country" => "Sierra Leone ",
                "filterValue" => "SL",
                "DialingCode" => "232",
            ],
            [
                "Country" => "Singapore ",
                "filterValue" => "SG",
                "DialingCode" => "65",
            ],
            [
                "Country" => "Slovakia",
                "filterValue" => "SK",
                "DialingCode" => "421",
            ],
            [
                "Country" => "Slovenia ",
                "filterValue" => "SI",
                "DialingCode" => "386",
            ],
            [
                "Country" => "Solomon Islands ",
                "filterValue" => "SB",
                "DialingCode" => "677",
            ],
            [
                "Country" => "Somalia ",
                "filterValue" => "SO",
                "DialingCode" => "252",
            ],
            [
                "Country" => "South Africa",
                "filterValue" => "ZA",
                "DialingCode" => "27",
            ],
            [
                "Country" => "Spain ",
                "filterValue" => "ES",
                "DialingCode" => "34",
            ],
            [
                "Country" => "Sri Lanka ",
                "filterValue" => "LK",
                "DialingCode" => "94",
            ],
            [
                "Country" => "Sudan ",
                "filterValue" => "SD",
                "DialingCode" => "249",
            ],
            [
                "Country" => "Suriname ",
                "filterValue" => "SR",
                "DialingCode" => "597",
            ],
            [
                "Country" => "Swaziland",
                "filterValue" => "SZ",
                "DialingCode" => "268",
            ],
            [
                "Country" => "Sweden ",
                "filterValue" => "SE",
                "DialingCode" => "46",
            ],
            [
                "Country" => "Switzerland ",
                "filterValue" => "CH",
                "DialingCode" => "41",
            ],
            [
                "Country" => "Syria",
                "filterValue" => "SY",
                "DialingCode" => "963",
            ],
            [
                "Country" => "Taiwan ",
                "filterValue" => "TW",
                "DialingCode" => "886",
            ],
            [
                "Country" => "Tajikistan ",
                "filterValue" => "TJ",
                "DialingCode" => "992",
            ],
            [
                "Country" => "Tanzania",
                "filterValue" => "TZ",
                "DialingCode" => "255",
            ],
            [
                "Country" => "Thailand ",
                "filterValue" => "TH",
                "DialingCode" => "66",
            ],
            [
                "Country" => "Tokelau ",
                "filterValue" => "TK",
                "DialingCode" => "690",
            ],
            [
                "Country" => "Tonga",
                "filterValue" => "TO",
                "DialingCode" => "676",
            ],
            [
                "Country" => "Trinidad and Tobago ",
                "filterValue" => "TT",
                "DialingCode" => "1868",
            ],
            [
                "Country" => "Tunisia ",
                "filterValue" => "TN",
                "DialingCode" => "216",
            ],
            [
                "Country" => "Turkey ",
                "filterValue" => "TR",
                "DialingCode" => "90",
            ],
            [
                "Country" => "Turkmenistan ",
                "filterValue" => "TM",
                "DialingCode" => "993",
            ],
            [
                "Country" => "Turks and Caicos Islands ",
                "filterValue" => "TC",
                "DialingCode" => "1649",
            ],
            [
                "Country" => "Tuvalu ",
                "filterValue" => "TV",
                "DialingCode" => "688",
            ],
            [
                "Country" => "Uganda",
                "filterValue" => "UG",
                "DialingCode" => "256",
            ],
            [
                "Country" => "Ukraine",
                "filterValue" => "UA",
                "DialingCode" => "380",
            ],
            [
                "Country" => "United Arab Emirates",
                "filterValue" => "AE",
                "DialingCode" => "971",
            ],
            [
                "Country" => "United Kingdom ",
                "filterValue" => "GB",
                "DialingCode" => "44",
            ],
            [
                "Country" => "Uruguay",
                "filterValue" => "UY",
                "DialingCode" => "598",
            ],
            [
                "Country" => "Uzbekistan",
                "filterValue" => "UZ",
                "DialingCode" => "998",
            ],
            [
                "Country" => "Vanuatu ",
                "filterValue" => "VU",
                "DialingCode" => "678",
            ],
            [
                "Country" => "Vatican City State ",
                "filterValue" => "VA",
                "DialingCode" => "418",
            ],
            [
                "Country" => "Venezuela ",
                "filterValue" => "VE",
                "DialingCode" => "58",
            ],
            [
                "Country" => "Vietnam ",
                "filterValue" => "VN",
                "DialingCode" => "84",
            ],
            [
                "Country" => "Virgin Islands, British ",
                "filterValue" => "VI",
                "DialingCode" => "1284",
            ],
            [
                "Country" => "Virgin Islands, United States",
                "filterValue" => "VQ",
                "DialingCode" => "1340",
            ],
            [
                "Country" => "Wallis and Futuna Islands ",
                "filterValue" => "WF",
                "DialingCode" => "681",
            ],
            [
                "Country" => "Yemen ",
                "filterValue" => "YE",
                "DialingCode" => "967",
            ],
            [
                "Country" => "Zambia",
                "filterValue" => "ZM",
                "DialingCode" => "260",
            ],
            [
                "Country" => "Zimbabwe",
                "filterValue" => "ZW",
                "DialingCode" => "263",
            ]
        ];
        $filters = [
            [
                "name" => "Countries",
                "source" => $countriesJson
            ]
        ];
        collect($filters)->map(function ($filter) {
            Filter::create($filter);
        });
    }
}
