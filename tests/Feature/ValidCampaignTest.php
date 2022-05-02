<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ValidCampaignTest extends TestCase
{
    public $authuser = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjb21wYW55Ijp7ImlkIjoiMjc4MjU4IiwibmFtZSI6IkF1dG9yb3V0aW5naW5kaWEgMSIsInVzZXJuYW1lIjoiYXV0b3JvdXRpbmdpbmRpYSIsIm1vYm5vIjoiOTE3MDAwNjc4MjkyIiwiZW1haWwiOiJNZWhhQGdpZGRoLmNvbSJ9LCJ1c2VyIjp7ImlkIjoiMjc5MTIzIiwibmFtZSI6IlBhcmVzaCBKYWlzaW5naGFuaSIsInVzZXJuYW1lIjoicGFyZXNoQHdob3p6YXQuY29tIiwibW9ibm8iOiI5MTcyMjM4NTQ1OTQiLCJlbWFpbCI6InBhcmVzaEB3aG96emF0LmNvbSIsInBlcm1pc3Npb25zIjoiW1wiI3NlbmRfc21zXCIsXCIjcGhvbmVib29rXCIsXCIjZGV2ZWxvcGVyXCIsXCIjYXBpXCIsXCIjb3RwXCIsXCIjc2VuZF9lbWFpbFwiLFwiI3RyYW5zYWN0aW9uYWwtZW1haWxzLXJlcG9ydHNcIixcIiN0cmFuc2FjdGlvbmFsLWVtYWlsLWJvdW5jZWRcIixcIiN0cmFuc2FjdGlvbmFsLWVtYWlsc1wiLFwiI3ZvaWNlX3Ntc1wiLFwiI3ZvaWNlX2NhbGxzX3JlcG9ydHNcIixcIiNtYW5hZ2VfcmVzZWxsZXJcIixcIiNsY2lcIixcIiN0cmFuc2FjdGlvbi1sb2dzXCIsXCIjZXhwb3J0c1wiLFwiI2ludGVncmF0aW9uX3NldHRpbmdcIixcIiNlZGl0X2Zsb3dcIixcIiNyZXF1ZXN0X2RpZF9udW1iZXJzXCIsXCIja25vd2xlZGdlX2Jhc2VcIixcIiNhdXRoa2V5XCIsXCIjY2FsbF9sb2dcIixcIiNjb250YWN0X2NlbnRlclwiLFwiI2xlYWRzXCJdIiwibWljcm9zZXJ2aWNlX3Blcm1pc3Npb25zIjpbeyJtaWNyb3NlcnZpY2VJZCI6MSwibWljcm9zZXJ2aWNlTmFtZSI6IlNNUyIsInBlcm1pc3Npb24iOlt7InBlcm1pc3Npb25JZCI6IjEiLCJwZXJtaXNzaW9uTmFtZSI6InNlbmRfc21zIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiU2VuZCBTTVMiLCJ2YWx1ZSI6IjEifSx7InBlcm1pc3Npb25JZCI6IjIiLCJwZXJtaXNzaW9uTmFtZSI6InBob25lYm9vayIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlBob25lYm9vayIsInZhbHVlIjoiMSJ9LHsicGVybWlzc2lvbklkIjoiMyIsInBlcm1pc3Npb25OYW1lIjoiZmxvdyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkZsb3ciLCJ2YWx1ZSI6IjEifSx7InBlcm1pc3Npb25JZCI6IjQiLCJwZXJtaXNzaW9uTmFtZSI6ImFwaSIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkFwaSIsInZhbHVlIjoiMSJ9LHsicGVybWlzc2lvbklkIjoiNSIsInBlcm1pc3Npb25OYW1lIjoib3RwIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiU2VuZCBPVFAiLCJ2YWx1ZSI6IjEifSx7InBlcm1pc3Npb25JZCI6IjYiLCJwZXJtaXNzaW9uTmFtZSI6InZvaWNlX3NtcyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlZvaWNlIFNNUyIsInZhbHVlIjoiMSJ9LHsicGVybWlzc2lvbklkIjoiNyIsInBlcm1pc3Npb25OYW1lIjoidmlydHVhbF9udW1iZXIiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJWaXJ0dWFsIE51bWJlciIsInZhbHVlIjoiMSJ9LHsicGVybWlzc2lvbklkIjoiOCIsInBlcm1pc3Npb25OYW1lIjoidHJhbnNhY3Rpb24tbG9ncyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlRyYW5zYWN0aW9uIGxvZ3MiLCJ2YWx1ZSI6IjEifSx7InBlcm1pc3Npb25JZCI6IjkiLCJwZXJtaXNzaW9uTmFtZSI6ImV4cG9ydHMiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJEYXRhIEV4cG9ydCIsInZhbHVlIjoiMSJ9XX0seyJtaWNyb3NlcnZpY2VJZCI6MiwibWljcm9zZXJ2aWNlTmFtZSI6IkVtYWlsIiwicGVybWlzc2lvbiI6W3sicGVybWlzc2lvbklkIjoiMTAiLCJwZXJtaXNzaW9uTmFtZSI6Im91dGJvdW5kX21haWxzIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiT3V0Ym91bmQgTWFpbHMiLCJ2YWx1ZSI6IjIifSx7InBlcm1pc3Npb25JZCI6IjExIiwicGVybWlzc2lvbk5hbWUiOiJyZXBvcnRzIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiUmVwb3J0cyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMTIiLCJwZXJtaXNzaW9uTmFtZSI6InRlbXBsYXRlcyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlRlbXBsYXRlcyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMTMiLCJwZXJtaXNzaW9uTmFtZSI6ImRvbWFpbnMiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJEb21haW5zIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIxNCIsInBlcm1pc3Npb25OYW1lIjoibG9ncyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkxvZ3MiLCJ2YWx1ZSI6IjMifSx7InBlcm1pc3Npb25JZCI6IjE1IiwicGVybWlzc2lvbk5hbWUiOiJpbmJvdW5kX2VtYWlscyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkluYm91bmQgRW1haWxzIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIxNiIsInBlcm1pc3Npb25OYW1lIjoid2ViaG9va3MiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJXZWJob29rcyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMTciLCJwZXJtaXNzaW9uTmFtZSI6ImFuYWx5dGljcyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkFuYWx5dGljcyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMTgiLCJwZXJtaXNzaW9uTmFtZSI6InN1cHByZXNzaW9ucyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlN1cHByZXNzaW9ucyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMzciLCJwZXJtaXNzaW9uTmFtZSI6ImRvbWFpbl9zZXR0aW5ncyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkRvbWFpbiBTZXR0aW5ncyIsInZhbHVlIjoiMiJ9XX0seyJtaWNyb3NlcnZpY2VJZCI6MywibWljcm9zZXJ2aWNlTmFtZSI6IlZvaWNlIiwicGVybWlzc2lvbiI6W3sicGVybWlzc2lvbklkIjoiMTkiLCJwZXJtaXNzaW9uTmFtZSI6ImxvZ3MiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJMb2dzIiwidmFsdWUiOiIzIn0seyJwZXJtaXNzaW9uSWQiOiIyMCIsInBlcm1pc3Npb25OYW1lIjoic2VuZF92b2ljZSIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlNlbmQgVm9pY2UiLCJ2YWx1ZSI6IjIifSx7InBlcm1pc3Npb25JZCI6IjM4IiwicGVybWlzc2lvbk5hbWUiOiJmaWxlcyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkZpbGVzIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIzOSIsInBlcm1pc3Npb25OYW1lIjoidGVtcGxhdGUiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJUZW1wbGF0ZSIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiNDAiLCJwZXJtaXNzaW9uTmFtZSI6InJlcG9ydHMiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJSZXBvcnRzIiwidmFsdWUiOiIyIn1dfSx7Im1pY3Jvc2VydmljZUlkIjo0LCJtaWNyb3NlcnZpY2VOYW1lIjoiV2hhdEFwcCIsInBlcm1pc3Npb24iOlt7InBlcm1pc3Npb25JZCI6IjIxIiwicGVybWlzc2lvbk5hbWUiOiJsb2dzIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiTG9ncyIsInZhbHVlIjoiMyJ9LHsicGVybWlzc2lvbklkIjoiMjIiLCJwZXJtaXNzaW9uTmFtZSI6InNlbmRfd2hhdHNhcHAiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJTZW5kIFdoYXRzYXBwIiwidmFsdWUiOiIyIn1dfSx7Im1pY3Jvc2VydmljZUlkIjo1LCJtaWNyb3NlcnZpY2VOYW1lIjoiUkNTIiwicGVybWlzc2lvbiI6W3sicGVybWlzc2lvbklkIjoiMjMiLCJwZXJtaXNzaW9uTmFtZSI6ImxvZ3MiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJMb2dzIiwidmFsdWUiOiIzIn0seyJwZXJtaXNzaW9uSWQiOiIyNCIsInBlcm1pc3Npb25OYW1lIjoic2VuZF9yY3MiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJTZW5kIFJDUyIsInZhbHVlIjoiMiJ9XX0seyJtaWNyb3NlcnZpY2VJZCI6NiwibWljcm9zZXJ2aWNlTmFtZSI6IkNhbXBhaWduIiwicGVybWlzc2lvbiI6W3sicGVybWlzc2lvbklkIjoiMjUiLCJwZXJtaXNzaW9uTmFtZSI6ImNhbXBhaWduIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiQ2FtcGFpZ24iLCJ2YWx1ZSI6IjIifSx7InBlcm1pc3Npb25JZCI6IjI2IiwicGVybWlzc2lvbk5hbWUiOiJ0b2tlbiIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlRva2VuIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIyNyIsInBlcm1pc3Npb25OYW1lIjoibG9ncyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkxvZ3MiLCJ2YWx1ZSI6IjMifV19LHsibWljcm9zZXJ2aWNlSWQiOjcsIm1pY3Jvc2VydmljZU5hbWUiOiJIZWxsbyIsInBlcm1pc3Npb24iOlt7InBlcm1pc3Npb25JZCI6IjI4IiwicGVybWlzc2lvbk5hbWUiOiJrbm93bGVkZ2VfYmFzZSIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6Iktub3dsZWRnZSBCYXNlIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIyOSIsInBlcm1pc3Npb25OYW1lIjoiYW5hbHlzaXMiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJBbmFseXNpcyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMzAiLCJwZXJtaXNzaW9uTmFtZSI6ImludGVncmF0aW9ucyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkludGVncmF0aW9ucyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMzEiLCJwZXJtaXNzaW9uTmFtZSI6ImJsb2NrZWRfY2xpZW50cyIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IkJsb2NrZWQgQ2xpZW50cyIsInZhbHVlIjoiMiJ9LHsicGVybWlzc2lvbklkIjoiMzIiLCJwZXJtaXNzaW9uTmFtZSI6ImNvbnRhY3RfY2VudGVyIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiQ29udGFjdCBDZW50ZXIiLCJ2YWx1ZSI6IjIifSx7InBlcm1pc3Npb25JZCI6IjMzIiwicGVybWlzc2lvbk5hbWUiOiJ2b2ljZSIsInBlcm1pc3Npb25EaXNwbGF5TmFtZSI6IlZvaWNlIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIzNCIsInBlcm1pc3Npb25OYW1lIjoiZW1haWxfdGlja2V0IiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiRW1haWwgVGlja2V0IiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIzNSIsInBlcm1pc3Npb25OYW1lIjoiYW5hbHl0aWNzIiwicGVybWlzc2lvbkRpc3BsYXlOYW1lIjoiQW5hbHl0aWNzIiwidmFsdWUiOiIyIn0seyJwZXJtaXNzaW9uSWQiOiIzNiIsInBlcm1pc3Npb25OYW1lIjoidGVhbV9tYW5hZ2UiLCJwZXJtaXNzaW9uRGlzcGxheU5hbWUiOiJNYW5hZ2UgVGVhbSIsInZhbHVlIjoiMiJ9XX1dfSwiaWQiOiIyNWFrdnJpbWdwaXJmNTRjYmVqdjMwdTZzdiIsImN1cnJlbmN5IjoiSU5SIiwiZ2RwckRhdGEiOnsicmVnaW9uTmFtZSI6IkluZGlhIiwicmVnaW9uU2hvcnRuYW1lIjoiSU4ifX0.dTcwFsMlPBujo-8G9Rmlhdq7w69XC9WQ7lKIsoQjLZU";

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_passing_empty_campaign_name()
    {
        $reqbody = array(
            'module_type' => 'flow',
            'name' => ""
        );

        $res = $this->withHeader(
            'Authorization',
            $this->authuser
        )->post('api/campaigns', $reqbody)
            ->assertSuccessful();
            $res=json_decode($res->getContent());
        $this->assertTrue($res->errors[0]=="The name field is required.");
    }

    public function test_passing_invalid_campaign_name()
    {
        $reqbody = array(
            'module_type' => 'flow',
            'name' => "hello-test "
        );

        $res = $this->withHeader(
            'Authorization',
            $this->authuser
        )->post('api/campaigns', $reqbody)
            ->assertSuccessful();
            $res=json_decode($res->getContent());
        $this->assertTrue($res->errors[0]=="The name format is invalid.");
    }

    public function test_campaign_name_has_less_than_3_words()
    {
        $reqbody = array(
            'module_type' => 'flow',
            'name' => "p"
        );

        $res = $this->withHeader(
            'Authorization',
            $this->authuser
        )->post('api/campaigns', $reqbody)
            ->assertSuccessful();
            $res=json_decode($res->getContent());
        $this->assertTrue($res->errors[0]=="The name must be at least 3 characters long.");
    }
    public function test_campaign_name_has_more_than_50_words()
    {
        $reqbody = array(
            'module_type' => 'flow',
            'name' => "ahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfjahdjdsjjskljhdfjdjhfjndsfhdsfj"
        );

        $res = $this->withHeader(
            'Authorization',
            $this->authuser
        )->post('api/campaigns', $reqbody)
            ->assertSuccessful();
            $res=json_decode($res->getContent());
        $this->assertTrue($res->errors[0]=="The name must not be greater than 50 characters long.");
    }

}
