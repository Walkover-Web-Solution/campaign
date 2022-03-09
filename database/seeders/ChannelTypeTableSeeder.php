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
<<<<<<< HEAD
                            "type" => "object",
=======
                            "type" => "dropdown",
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
                            "template" => array(
                                "name" => "",
                                "template_id" => ""
                            ),
<<<<<<< HEAD
=======
                            "source" => "",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
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
<<<<<<< HEAD
                            "label" => 'BCC',
=======
                            "label" => '',
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
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
<<<<<<< HEAD
=======
                            "source" => "",
                            "sourceFieldLabel" => "name",
                            "sourceFieldValue" => "name",
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
                            "is_required" => true
                        )

                    ),
                    "mapping" => array(
                        array(
                            "name" => "mobiles",
<<<<<<< HEAD
                            "type" => 'text',
=======
                            "type" => 'list',
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
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
<<<<<<< HEAD
                            "label" => 'BCC',
=======
                            "label" => '',
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
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
<<<<<<< HEAD
                "configurations" => new \StdClass
=======
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
                            "is_required" => true
                        )
                    )
                )
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
            ],

            [
                "name" => "whatsapp",
                "configurations" => array(
                    "fields" => array(),
<<<<<<< HEAD
                    "mapping" => array()
=======
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
                            "is_required" => true
                        )
                    )
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
                )
            ],

            [
                "name" => "voice",
                "configurations" => array(
                    "fields" => array(),
<<<<<<< HEAD
                    "mapping" => array()
=======
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
                            "is_required" => true
                        )
                    )
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
                )
            ],
        ];

        foreach ($channelTypes as $channelType) {
<<<<<<< HEAD
            $channelTypeObj = ChannelType::withoutGlobalScopes()->where('name', $channelType['name'])->first();
=======
            $channelTypeObj = ChannelType::where('name', $channelType['name'])->first();
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
            if (empty($channelTypeObj)) {
                ChannelType::create($channelType);
            } else {
                $channelTypeObj->update($channelType);
            }
        }
    }
}
