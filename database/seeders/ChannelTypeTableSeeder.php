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

        ChannelType::truncate();

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
                                "template_id" => "",
                                "slug" => ""
                            ),
                            "source" => "",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
                            "is_required" => true,
                            "value" => "",
                            "variables" => []
                        ),
                        array(
                            "name" => "from_email",
                            "type" => 'text',
                            "label" => 'From Email',
                            "regex" => "^[A-Za-z\d\.]+$",
                            "min" => 1,
                            "max" => 50,
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "value" => "",
                            "placeholder" => "You can email that will be shown to recipient."
                        ),
                        array(
                            "name" => "domain",
                            "type" => 'dropdown',
                            "label" => 'Select  Domain',
                            "regex" => "",
                            "source" => "domains?is_enabled=1&status_id=2",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "value" => ""
                        ),
                        array(
                            "name" => "parent_domain",
                            "type" => 'dropdown',
                            "label" => 'Select Parent Domain',
                            "regex" => "",
                            "source" => "domains?is_enabled=1&status_id=2",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
                            "is_required" => true,
                            "value" => ""
                        ),
                        array(
                            "name" => "from_email_name",
                            "type" => 'text',
                            "label" => 'From Email Name',
                            "regex" => '^[A-Za-z\d\s]+$',
                            "min" => 1,
                            "max" => 50,
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "value" => "",
                            "placeholder" => "You can define name that will be shown to recipient."
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
                            "value" => ""
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
                            "value" => ""
                        ),
                        array(
                            "name" => "delay",
                            "label" => "Delay for",
                            "type" => "text",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "value" => "0"
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
                            "is_required" => true,
                            "variables" => []
                        ),
                        array(
                            "name" => "delay",
                            "label" => "Delay for",
                            "type" => "text",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "value" => "0"
                        )

                    ),
                    "mapping" => array(
                        array(
                            "name" => "mobiles",
                            "type" => 'list',
                            "is_required" => true,
                            "label" => 'Mobiles',
                            "regex" => "^([\d],?)+$",
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
                "name" => "Whatsapp",
                "configurations" => array(
                    "fields" => array(
                        array(
                            "name" => "integrated_number",
                            "label" => "Integrated number",
                            "type" => "dropdown",
                            "source" => "",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
                            "is_required" => true,
                            "value" => ""
                        )
                    ),
                    "mapping" => array(
                        array(
                            "name" => "mobiles",
                            "type" => 'list',
                            "is_required" => true,
                            "label" => 'Mobiles',
                            "regex" => "^([\d],?)+$",
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
                "name" => "Voice",
                "configurations" => array(
                    "fields" => array(),
                    "mapping" => array(
                        array(
                            "name" => "mobiles",
                            "type" => 'list',
                            "is_required" => true,
                            "label" => 'Mobiles',
                            "regex" => "^([\d],?)+$",
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
                "name" => "RCS",
                "configurations" => array(
                    "fields" => array(),
                    "mapping" => array()
                )
            ],

            [
                "name" => "Condition",
                "configurations" => array(
                    "fields" => array(
                        array(
                            "name" => "delay",
                            "label" => "Delay for",
                            "type" => "text",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "value" => "0"
                        ),
                        array(
                            "name" => "Condition",
                            "label" => "Condition",
                            "type" => "text",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "value" => ""
                        )
                    ),
                    "mapping" => array()
                )
            ]
        ];

        collect($channelTypes)->map(function ($channelType) {
            ChannelType::create($channelType);
        });
    }
}
