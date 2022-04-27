<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\TestCase;

class BasicTest extends TestCase
{
    public $authuser = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjb21wYW55Ijp7ImlkIjoiMjc4MjU4IiwibmFtZSI6IkF1dG9yb3V0aW5naW5kaWEgMSIsInVzZXJuYW1lIjoiYXV0b3JvdXRpbmdpbmRpYSIsIm1vYm5vIjoiOTE3MDAwNjc4MjkyIiwiZW1haWwiOiJNZWhhQGdpZGRoLmNvbSJ9LCJ1c2VyIjp7ImlkIjoiMjc5MTIzIiwibmFtZSI6IlBhcmVzaCBKYWlzaW5naGFuaSIsInVzZXJuYW1lIjoicGFyZXNoQHdob3p6YXQuY29tIiwibW9ibm8iOiI5MTcyMjM4NTQ1OTQiLCJlbWFpbCI6InBhcmVzaEB3aG96emF0LmNvbSIsInBlcm1pc3Npb25zIjoiW1wiI3NlbmRfc21zXCIsXCIjcGhvbmVib29rXCIsXCIjZGV2ZWxvcGVyXCIsXCIjYXBpXCIsXCIjb3RwXCIsXCIjc2VuZF9lbWFpbFwiLFwiI3RyYW5zYWN0aW9uYWwtZW1haWxzLXJlcG9ydHNcIixcIiN0cmFuc2FjdGlvbmFsLWVtYWlsLWJvdW5jZWRcIixcIiN0cmFuc2FjdGlvbmFsLWVtYWlsc1wiLFwiI3ZvaWNlX3Ntc1wiLFwiI3ZvaWNlX2NhbGxzX3JlcG9ydHNcIixcIiNtYW5hZ2VfcmVzZWxsZXJcIixcIiNsY2lcIixcIiN0cmFuc2FjdGlvbi1sb2dzXCIsXCIjZXhwb3J0c1wiLFwiI2ludGVncmF0aW9uX3NldHRpbmdcIixcIiNlZGl0X2Zsb3dcIixcIiNyZXF1ZXN0X2RpZF9udW1iZXJzXCIsXCIja25vd2xlZGdlX2Jhc2VcIixcIiNhdXRoa2V5XCIsXCIjY2FsbF9sb2dcIixcIiNjb250YWN0X2NlbnRlclwiLFwiI2xlYWRzXCJdIiwibWljcm9zZXJ2aWNlX3Blcm1pc3Npb25zIjpbeyJtaWNyb3NlcnZpY2VJZCI6MSwibWljcm9zZXJ2aWNlTmFtZSI6IlNNUyIsInBlcm1pc3Npb24iOlt7InBlcm1pc3Npb25JZCI6IjEiLCJwZXJtaXNzaW9uTmFtZSI6InNlbmRfc21zIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiU2VuZCBTTVMiLCJ2YWx1ZSI6IjEifSx7InBlcm1pc3Npb25JZCI6IjIiLCJwZXJtaXNzaW9uTmFtZSI6InBob25lYm9vayIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlBob25lYm9vayIsInZhbHVlIjoiMSJ9LHsicGVybWlzc2lvbklkIjoiMyIsInBlcm1pc3Npb25OYW1lIjoiZmxvdyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkZsb3ciLCJ2YWx1ZSI6IjEifSx7InBlcm1pc3Npb25JZCI6IjQiLCJwZXJtaXNzaW9uTmFtZSI6ImFwaSIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkFwaSIsInZhbHVlIjoiMSJ9LHsicGVybWlzc2lvbklkIjoiNSIsInBlcm1pc3Npb25OYW1lIjoib3RwIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiU2VuZCBPVFAiLCJ2YWx1ZSI6IjEifSx7InBlcm1pc3Npb25JZCI6IjYiLCJwZXJtaXNzaW9uTmFtZSI6InZvaWNlX3NtcyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlZvaWNlIFNNUyIsInZhbHVlIjoiMSJ9LHsicGVybWlzc2lvbklkIjoiNyIsInBlcm1pc3Npb25OYW1lIjoidmlydHVhbF9udW1iZXIiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJWaXJ0dWFsIE51bWJlciIsInZhbHVlIjoiMSJ9LHsicGVybWlzc2lvbklkIjoiOCIsInBlcm1pc3Npb25OYW1lIjoidHJhbnNhY3Rpb24tbG9ncyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlRyYW5zYWN0aW9uIGxvZ3MiLCJ2YWx1ZSI6IjEifSx7InBlcm1pc3Npb25JZCI6IjkiLCJwZXJtaXNzaW9uTmFtZSI6ImV4cG9ydHMiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJEYXRhIEV4cG9ydCIsInZhbHVlIjoiMSJ9XX0seyJtaWNyb3NlcnZpY2VJZCI6MiwibWljcm9zZXJ2aWNlTmFtZSI6IkVtYWlsIiwicGVybWlzc2lvbiI6W3sicGVybWlzc2lvbklkIjoiMTAiLCJwZXJtaXNzaW9uTmFtZSI6Im91dGJvdW5kX21haWxzIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiT3V0Ym91bmQgTWFpbHMiLCJ2YWx1ZSI6IjIifSx7InBlcm1pc3Npb25JZCI6IjExIiwicGVybWlzc2lvbk5hbWUiOiJyZXBvcnRzIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiUmVwb3J0cyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMTIiLCJwZXJtaXNzaW9uTmFtZSI6InRlbXBsYXRlcyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlRlbXBsYXRlcyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMTMiLCJwZXJtaXNzaW9uTmFtZSI6ImRvbWFpbnMiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJEb21haW5zIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIxNCIsInBlcm1pc3Npb25OYW1lIjoibG9ncyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkxvZ3MiLCJ2YWx1ZSI6IjMifSx7InBlcm1pc3Npb25JZCI6IjE1IiwicGVybWlzc2lvbk5hbWUiOiJpbmJvdW5kX2VtYWlscyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkluYm91bmQgRW1haWxzIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIxNiIsInBlcm1pc3Npb25OYW1lIjoid2ViaG9va3MiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJXZWJob29rcyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMTciLCJwZXJtaXNzaW9uTmFtZSI6ImFuYWx5dGljcyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkFuYWx5dGljcyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMTgiLCJwZXJtaXNzaW9uTmFtZSI6InN1cHByZXNzaW9ucyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlN1cHByZXNzaW9ucyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMzciLCJwZXJtaXNzaW9uTmFtZSI6ImRvbWFpbl9zZXR0aW5ncyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkRvbWFpbiBTZXR0aW5ncyIsInZhbHVlIjoiMiJ9XX0seyJtaWNyb3NlcnZpY2VJZCI6MywibWljcm9zZXJ2aWNlTmFtZSI6IlZvaWNlIiwicGVybWlzc2lvbiI6W3sicGVybWlzc2lvbklkIjoiMTkiLCJwZXJtaXNzaW9uTmFtZSI6ImxvZ3MiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJMb2dzIiwidmFsdWUiOiIzIn0seyJwZXJtaXNzaW9uSWQiOiIyMCIsInBlcm1pc3Npb25OYW1lIjoic2VuZF92b2ljZSIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlNlbmQgVm9pY2UiLCJ2YWx1ZSI6IjIifSx7InBlcm1pc3Npb25JZCI6IjM4IiwicGVybWlzc2lvbk5hbWUiOiJmaWxlcyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkZpbGVzIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIzOSIsInBlcm1pc3Npb25OYW1lIjoidGVtcGxhdGUiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJUZW1wbGF0ZSIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiNDAiLCJwZXJtaXNzaW9uTmFtZSI6InJlcG9ydHMiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJSZXBvcnRzIiwidmFsdWUiOiIyIn1dfSx7Im1pY3Jvc2VydmljZUlkIjo0LCJtaWNyb3NlcnZpY2VOYW1lIjoiV2hhdEFwcCIsInBlcm1pc3Npb24iOlt7InBlcm1pc3Npb25JZCI6IjIxIiwicGVybWlzc2lvbk5hbWUiOiJsb2dzIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiTG9ncyIsInZhbHVlIjoiMyJ9LHsicGVybWlzc2lvbklkIjoiMjIiLCJwZXJtaXNzaW9uTmFtZSI6InNlbmRfd2hhdHNhcHAiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJTZW5kIFdoYXRzYXBwIiwidmFsdWUiOiIyIn1dfSx7Im1pY3Jvc2VydmljZUlkIjo1LCJtaWNyb3NlcnZpY2VOYW1lIjoiUkNTIiwicGVybWlzc2lvbiI6W3sicGVybWlzc2lvbklkIjoiMjMiLCJwZXJtaXNzaW9uTmFtZSI6ImxvZ3MiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJMb2dzIiwidmFsdWUiOiIzIn0seyJwZXJtaXNzaW9uSWQiOiIyNCIsInBlcm1pc3Npb25OYW1lIjoic2VuZF9yY3MiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJTZW5kIFJDUyIsInZhbHVlIjoiMiJ9XX0seyJtaWNyb3NlcnZpY2VJZCI6NiwibWljcm9zZXJ2aWNlTmFtZSI6IkNhbXBhaWduIiwicGVybWlzc2lvbiI6W3sicGVybWlzc2lvbklkIjoiMjUiLCJwZXJtaXNzaW9uTmFtZSI6ImNhbXBhaWduIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiQ2FtcGFpZ24iLCJ2YWx1ZSI6IjIifSx7InBlcm1pc3Npb25JZCI6IjI2IiwicGVybWlzc2lvbk5hbWUiOiJ0b2tlbiIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlRva2VuIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIyNyIsInBlcm1pc3Npb25OYW1lIjoibG9ncyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkxvZ3MiLCJ2YWx1ZSI6IjMifV19LHsibWljcm9zZXJ2aWNlSWQiOjcsIm1pY3Jvc2VydmljZU5hbWUiOiJIZWxsbyIsInBlcm1pc3Npb24iOlt7InBlcm1pc3Npb25JZCI6IjI4IiwicGVybWlzc2lvbk5hbWUiOiJrbm93bGVkZ2VfYmFzZSIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6Iktub3dsZWRnZSBCYXNlIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIyOSIsInBlcm1pc3Npb25OYW1lIjoiYW5hbHlzaXMiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJBbmFseXNpcyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMzAiLCJwZXJtaXNzaW9uTmFtZSI6ImludGVncmF0aW9ucyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkludGVncmF0aW9ucyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMzEiLCJwZXJtaXNzaW9uTmFtZSI6ImJsb2NrZWRfY2xpZW50cyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkJsb2NrZWQgQ2xpZW50cyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMzIiLCJwZXJtaXNzaW9uTmFtZSI6ImNvbnRhY3RfY2VudGVyIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiQ29udGFjdCBDZW50ZXIiLCJ2YWx1ZSI6IjIifSx7InBlcm1pc3Npb25JZCI6IjMzIiwicGVybWlzc2lvbk5hbWUiOiJ2b2ljZSIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlZvaWNlIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIzNCIsInBlcm1pc3Npb25OYW1lIjoiZW1haWxfdGlja2V0IiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiRW1haWwgVGlja2V0IiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIzNSIsInBlcm1pc3Npb25OYW1lIjoiYW5hbHl0aWNzIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiQW5hbHl0aWNzIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIzNiIsInBlcm1pc3Npb25OYW1lIjoidGVhbV9tYW5hZ2UiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJNYW5hZ2UgVGVhbSIsInZhbHVlIjoiMiJ9XX1dfSwiaWQiOiIyNWFrdnJpbWdwaXJmNTRjYmVqdjMwdTZzdiIsImN1cnJlbmN5IjoiSU5SIiwiZ2RwckRhdGEiOnsicmVnaW9uTmFtZSI6IkluZGlhIiwicmVnaW9uU2hvcnRuYW1lIjoiSU4ifX0.dTcwFsMlPBujo-8G9Rmlhdq7w69XC9WQ7lKIsoQjLZU";

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_user()
    {
        $key = Client::where('id', 2)->first();
        $this->withHeaders([
            'Authorization' => $this->authuser,
            'Authkey' => $key->authkey,
        ])->post('api/register')
            ->assertStatus(200);
    }

    public function test_create_campaign()
    {
        $reqbody = array(
            'module_type' => 'flow',
            'name' => "test1",
        );
        $this->withHeader(
            'Authorization',
            $this->authuser
        )->post('api/campaigns', $reqbody)
            ->assertStatus(201);
    }

    public function test_create_flow_action()
    {
        $email = array(

            "module_type" => "Email",
            "name" => "Email_1",
            "channel_id" => 1,
            "configurations" => array(
                array(
                    "name" => "template",
                    "type" => "dropdown",
                    "value" => "",
                    "source" => "",
                    "template" => [
                        "name" => "template",
                        "slug" => "mohit_test",
                        "template_id" => "mohit_test"
                    ],
                    "variables" => [
                        "ORG_LOGO_LINK",
                        "ORG_NAME",
                        "MENU1_LINK",
                        "MENU1_TEXT",
                        "MENU2_LINK",
                        "MENU2_TEXT",
                        "MENU3_LINK",
                        "MENU3_TEXT",
                        "MENU4_LINK",
                        "MENU4_TEXT",
                        "BACKGROUND_IMAGE_SRC",
                        "BANNER_LINK",
                        "BANNER_IMAGE_SRC",
                        "MSG_SUBJECT",
                        "BTN_LINK",
                        "BTN_TXT",
                        "CLICK_GIF_SRC",
                        "MSG_BODY",
                        "FB_SOCIAL_LINK",
                        "INSTAGRAM_SOCIAL_LINK",
                        "TWITTER_SOCIAL_LINK",
                        "YOUTUBE_SOCIAL_LINK",
                        "ORG_LOGO_SRC",
                        "ORG_ADDRESS",
                        "CONTACT_NUMS"
                    ],
                    "is_required" => true,
                    "sourceFieldLabel" => "name",
                    "sourceFieldValue" => "name"
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
                    "value" => "paresh",
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
                    "value" => "mailerautoroutingindia162072635593.foo.taskb.in"
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
                    "value" => "mailerautoroutingindia162072635593.foo.taskb.in"
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
                    "value" => "paresh",
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
                    "value" => "paresh@whozzat.com"
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
                )
            )
        );

        $sms = array(

            "module_type" => "SMS",
            "name" => "SMS_1",
            "channel_id" => 2,
            "configurations" => array(
                array(
                    "name" => "template",
                    "type" => "object",
                    "template" => array(
                        "name" => "valid-flow",
                        "template_id" => "609bb138bd6b2326c7429542"
                    ),
                    "source" => "",
                    "sourceFieldLabel" => "name",
                    "sourceFieldValue" => "name",
                    "is_required" => true,
                    "variables" => [
                        "NAME",
                        "var1",
                        "var2",
                        "var"
                    ]
                )
            )
        );
        $this->withHeader(
            'Authorization',
            $this->authuser
        )->post('api/test1/flowActions', $email)
            ->assertStatus(201);

        $res = $this->withHeader(
            'Authorization',
            $this->authuser
        )->post('api/test1/flowActions', $sms)
            ->assertStatus(201);
    }

    public function test_update_campaign()
    {
        $update = array(
            "module_data" => [
                "op_start" => 1,
                "op_start_type" => "Email",
            ]
        );

        $res = $this->withHeader(
            'Authorization',
            $this->authuser
        )->json('PUT', '/api/campaigns/test1', $update)
            ->assertStatus(200);
    }

    public function test_update_flow_action()
    {
        $flow_update = array(
            "module_data" => array(
                "op_success" => 2,
                "op_success_type" => "SMS",
                "op_failed" => null,
                "op_failed_type" => null
            )
        );

        $res = $this->withHeader(
            'Authorization',
            $this->authuser
        )->json('PUT', '/api/test1/flowActions/1', $flow_update)
            ->assertStatus(200);
    }

    public function test_run_campaign()
    {
        $data = array(
            "data" => [
                "sendTo" => array(
                    array(
                        "to" => [
                            array(
                                "mobiles" => "919754710770",
                                "name" => "Prasuk",
                                "email" => ""
                            ),
                            array(
                                "mobiles" => "917223854594",
                                "name" => "Paresh",
                                "email" => "paresh@whhozzat.com"
                            )
                        ],
                        "bcc" => [
                            array(
                                "mobiles" => "",
                                "name" => "ravi",
                                "email" => "ravisoni@walkover.in"
                            )
                        ],
                        "variables" => [
                            "ORG_LOGO_LINK" => "ORG_LOGO_LINK",
                            "ORG_NAME" => "ORG_NAME",
                            "MENU1_LINK" => "MENU1_LINK",
                            "MENU1_TEXT" => "MENU1_TEXT",
                            "MENU2_LINK" => "MENU2_LINK",
                            "MENU2_TEXT" => "MENU2_TEXT",
                            "MENU3_LINK" => "MENU3_LINK",
                            "MENU3_TEXT" => "MENU3_TEXT",
                            "MENU4_LINK" => "MENU4_LINK",
                            "MENU4_TEXT" => "MENU4_TEXT",
                            "BACKGROUND_IMAGE_SRC" => "BACKGROUND_IMAGE_SRC",
                            "BANNER_LINK" => "BANNER_LINK",
                            "BANNER_IMAGE_SRC" => "BANNER_IMAGE_SRC",
                            "MSG_SUBJECT" => "MSG_SUBJECT",
                            "BTN_LINK" => "BTN_LINK",
                            "BTN_TXT" => "BTN_TXT",
                            "CLICK_GIF_SRC" => "CLICK_GIF_SRC",
                            "MSG_BODY" => "MSG_BODY",
                            "FB_SOCIAL_LINK" => "FB_SOCIAL_LINK",
                            "INSTAGRAM_SOCIAL_LINK" => "INSTAGRAM_SOCIAL_LINK",
                            "TWITTER_SOCIAL_LINK" => "TWITTER_SOCIAL_LINK",
                            "YOUTUBE_SOCIAL_LINK" => "YOUTUBE_SOCIAL_LINK",
                            "ORG_LOGO_SRC" => "ORG_LOGO_SRC",
                            "ORG_ADDRESS" => "ORG_ADDRESS",
                            "CONTACT_NUMS" => "CONTACT_NUMS",
                            "NAME" => "NAME",
                            "var1" => "var1",
                            "var2" => "var2",
                            "var" => "var"
                        ]
                    )
                )
            ]
        );

        $res = $this->withHeaders([
            'Authorization' => $this->authuser,
            'Accept' => "application/json",
        ])->post('api/campaigns/test1/run', $data)
            ->assertStatus(200);
    }


}
