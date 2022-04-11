<?php

namespace Database\Seeders;

use App\Models\ChannelType;
use Illuminate\Database\Seeder;

class ChannelTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $channelTypes = [
            [
                "name" => "Email",
                "configurations" => array(
                    "fields" => array(
                        array(
                            "name" => "template",
                            "type" => "dropdown",
                            "template" => array(
                                "name" => "",
                                "template_id" => ""
                            ),
                            "source" => "",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
                            "is_required" => true,
                            "value"=>""
                        ),
                        array(
                            "name" => "from_email",
                            "type" => 'text',
                            "label" => 'From Email',
                            "regex" => "[A-Za-z\d]",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "value"=>"",
                            "placeholder"=>"You can email that will be shown to recipient."
                        ),
                        array(
                            "name" => "domain",
                            "type" => 'dropdown',
                            "label" => 'Select  Domain',
                            "regex" => "",
                            "source" => "email/domains?is_enabled=1&status_id=2",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
                            "is_required" => true,
                            "value"=>""
                        ),
                        array(
                            "name" => "from_email_name",
                            "type" => 'text',
                            "label" => 'From Email Name',
                            "regex" => '[A-Za-z\d\s]',
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "value"=>"",
                            "placeholder"=>"You can define name that will be shown to recipient."
                        ),
                        
                        array(
                            "name" => "cc",
                            "type" => 'list',
                            "label" => 'CC',
                            "regex" => "^([\w+-.%]+@[\w.]+\.[A-Za-z]{2,4},?)+$",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "value"=>""
                        ),
                        array(
                            "name" => "bcc",
                            "type" => 'list',
                            "label" => 'BCC',
                            "regex" => "^([\w+-.%]+@[\w.]+\.[A-Za-z]{2,4},?)+$",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "value"=>""
                        )
                    ),
                    "mapping" => array(
                        array(
                            "name" => "to",
                            "type" => 'list',
                            "label" => 'To',
                            "regex" => "^([\w+-.%]+@[\w.]+\.[A-Za-z]{2,4},?)+$",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "is_array" => true
                        ),
                        array(
                            "name" => "cc",
                            "type" => 'list',
                            "label" => 'CC',
                            "regex" => "^([\w+-.%]+@[\w.]+\.[A-Za-z]{2,4},?)+$",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "is_array" => true
                        ),
                        array(
                            "name" => "bcc",
                            "type" => 'list',
                            "label" => 'BCC',
                            "regex" => "^([\w+-.%]+@[\w.]+\.[A-Za-z]{2,4},?)+$",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "is_array" => true
                        ),
                        array(
                            "name" => "variables",
                            "type" => 'list',
                            "label" => '',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "is_array" => true
                        )
                    )

                )
            ],
            [
                "name" => "SMS",
                "configurations" => array(
                    "fields" => array(
                        array(
                            "name" => "template",
                            "type" => "object",
                            "template" => array(
                                "name" => "",
                                "template_id" => ""
                            ),
                            "source" => "",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
                            "is_required" => true
                        )

                    ),
                    "mapping" => array(
                        array(
                            "name" => "mobiles",
                            "type" => 'list',
                            "is_required" => true,
                            "label" => 'Mobiles',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "is_array" => true
                        ),
                        array(
                            "name" => "variables",
                            "type" => 'list',
                            "label" => '',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "is_array" => true
                        )
                    )
                )

            ],

            [
                "name" => "OTP",
                "configurations" => array(
                    "fields" => array(),
                    "mapping" => array(
                        array(
                            "name" => "mobile",
                            "type" => 'list',
                            "label" => 'Mobile',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "is_array" => false
                        )
                    )
                )
            ],

            [
                "name" => "whatsapp",
                "configurations" => array(
                    "fields" => array(
                        array(
                            "name"=>"integrated_number",
                            "label" => "Integrated number",
                            "type" => "dropdown",
                            "source" => "",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
                            "is_required" => true,
                            "value"=>""
                        )
                    ),
                    "mapping" => array(
                        array(
                            "name" => "mobiles",
                            "type" => 'list',
                            "is_required" => true,
                            "label" => 'Mobiles',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "is_array" => true
                        )
                    )
                )
            ],

            [
                "name" => "voice",
                "configurations" => array(
                    "fields" => array(),
                    "mapping" => array(
                        array(
                            "name" => "mobiles",
                            "type" => 'list',
                            "is_required" => true,
                            "label" => 'Mobiles',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "is_array" => true
                        )
                    )
                )
            ],
        ];

        collect($channelTypes)->map(function ($channelType) {
            $channelTypeObj = ChannelType::where('name', $channelType['name'])->first();
            if (empty($channelTypeObj)) {
                ChannelType::create($channelType);
            } else {
                $channelTypeObj->update($channelType);
            }
        });
    }
}
