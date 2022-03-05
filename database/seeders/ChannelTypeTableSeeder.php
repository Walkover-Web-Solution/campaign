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
                            "type" => "object",
                            "template" => array(
                                "name" => "",
                                "template_id" => ""
                            ),
                            "is_required" => true
                        ),
                        array(
                            "name" => "domain",
                            "type" => 'dropdown',
                            "label" => 'Select  Domain',
                            "regex" => "",
                            "source" => "domains?is_enabled=1&status_id=2",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
                            "is_required" => true
                        ),
                        array(
                            "name" => "from_email_name",
                            "type" => 'text',
                            "label" => 'From Email Name',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "ui_visible" => true
                        ),
                        array(
                            "name" => "from_email",
                            "type" => 'text',
                            "label" => 'From Email',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true
                        ),
                        array(
                            "name" => "cc",
                            "type" => 'list',
                            "label" => 'CC',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false
                        ),
                        array(
                            "name" => "bcc",
                            "type" => 'list',
                            "label" => 'BCC',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false
                        )
                    ),
                    "mapping" => array(
                        array(
                            "name" => "to",
                            "type" => 'list',
                            "label" => 'To',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true
                        ),
                        array(
                            "name" => "cc",
                            "type" => 'list',
                            "label" => 'CC',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false
                        ),
                        array(
                            "name" => "bcc",
                            "type" => 'list',
                            "label" => 'BCC',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false
                        ),
                        array(
                            "name" => "variables",
                            "type" => 'list',
                            "label" => 'BCC',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false
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
                            "is_required" => true
                        )

                    ),
                    "mapping" => array(
                        array(
                            "name" => "mobiles",
                            "type" => 'text',
                            "is_required" => true,
                            "label" => 'Mobiles',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => true
                        ),
                        array(
                            "name" => "variables",
                            "type" => 'list',
                            "label" => 'BCC',
                            "regex" => "",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false
                        )
                    )
                )

            ],

            [
                "name" => "OTP",
                "configurations" => new \StdClass
            ],

            [
                "name" => "whatsapp",
                "configurations" => array(
                    "fields" => array(),
                    "mapping" => array()
                )
            ],

            [
                "name" => "voice",
                "configurations" => array(
                    "fields" => array(),
                    "mapping" => array()
                )
            ],
        ];

        foreach ($channelTypes as $channelType) {
            $channelTypeObj = ChannelType::withoutGlobalScopes()->where('name', $channelType['name'])->first();
            if (empty($channelTypeObj)) {
                ChannelType::create($channelType);
            } else {
                $channelTypeObj->update($channelType);
            }
        }
    }
}
