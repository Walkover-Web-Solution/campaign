<?php

namespace App\Rules;

use App\Exceptions\AttachmentTooLargeException;
use Illuminate\Contracts\Validation\Rule;

class BlobRule implements Rule
{
    private $errorMsg;
    public static $overAllSize;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!preg_match("/^data:.*;base64,.*/", $value)) {
            $this->errorMsg = $attribute . " field must be in the following format: data:content/type;base64";
            return false;
        }
        $explode = explode(',', $value);
        $mimeToExtensions = [
            'application/x-cdf' => 'cda', 'audio/midi' => 'mid', 'audio/x-midi' => 'midi', 'audio/mpeg' => 'mp3', 'audio/ogg' => 'oga', 'video/ogg' => 'ogv', 'application/ogg' => 'ogx', 'audio/wav' => 'wav', 'application/x-7z-compressed' => '7z', 'application/vnd.rar' => 'rar', 'application/x-tar' => 'tar', 'application/gzip' => 'gz', 'application/zip' => 'zip',
            'text/csv' => 'csv', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'xml', 'font/ttf' => 'ttf', 'image/bmp' => 'bmp', 'image/gif' => 'gif', 'image/vnd.microsoft.icon' => 'ico', 'image/jpeg' => 'jpeg', 'image/png' => 'png',
            'image/svg+xml' => 'svg', 'image/tiff' => 'tif', 'image/tiff' => 'tiff', 'text/html' => 'html', 'application/vnd.oasis.opendocument.spreadsheet' => 'ods', 'application/vnd.ms-excel' => 'xls', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx', 'video/3gpp2' => '3g2', 'video/3gpp' => '3gp',
            'video/x-msvideo' => 'avi', 'video/mp4' => 'mp4', 'video/mpeg' => 'mpeg', 'application/x-shockwave-flash' => 'swf',
            'application/msword' => 'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx', 'application/vnd.oasis.opendocument.text' => 'odt', 'application/pdf' => 'pdf', 'application/rtf' => 'rtf', 'text/plain' => 'txt', 'application/vnd.ms-powerpoint' => 'ppt', 'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx', 'application/json' => 'json'
        ];
        $mimeType = str_replace(
            ['data:', ';', 'base64'],
            ['', '', ''],
            $explode[0]
        );
        // check file size
        $filesize = strlen(base64_decode($explode[1]));
        if ($filesize > 2097152) {
            $this->errorMsg = $attribute . ' size must be less than 2 Mb';
            return false;
        } else {
            BlobRule::$overAllSize += $filesize;
            if ((BlobRule::$overAllSize + AttachmentRule::$overAllSize) > (6 * 1048576)) {
                $this->errormsg = 'Over all size must be less than 10 Mb';
                throw new AttachmentTooLargeException('Attachment too large, ' . $this->errormsg);
            }
        }

        // check file MIME type
        if (!in_array($mimeType, array_keys($mimeToExtensions))) {
            $this->errorMsg = $attribute . ", MIME type must be one of the following: " . implode(', ', array_keys($mimeToExtensions));
            return false;
        }
        $extension = $mimeToExtensions[$mimeType];
        request()->merge([$attribute . "Extension" => $extension]);
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMsg;
    }
}
