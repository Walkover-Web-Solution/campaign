<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AttachmentRule implements Rule
{
    private $errormsg;
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
        $extensions = [
            'aif', 'cda', 'mid', 'midi', 'mp3', 'mpa', 'ogg', 'oga', 'ogv', 'ogx', 'wav', 'wma',
            'wpl', '7z', 'arj', 'deb', 'pkg', 'rar', 'rpm', 'tar', 'gz', 'z', 'zip', 'dmg',
            'iso', 'vcd', 'csv', 'xml', 'email', 'eml', 'emlx', 'msg', 'oft', 'ost', 'pst', 'vcf',
            'fnt', 'fon', 'otf', 'ttf', 'ai', 'bmp', 'gif', 'ico', 'jpeg', 'jpg', 'png', 'ps', 'psd',
            'svg', 'tif', 'tiff', 'cer', 'cfm', 'html', 'ods', 'xls', 'xlsm', 'xlsx', '3g2', '3gp',
            'avi', 'flv', 'h264', 'm4v', 'mkv', 'mov', 'mp4', 'mpg', 'mpeg', 'rm', 'swf', 'vob', 'wmv',
            'doc', 'docx', 'odt', 'pdf', 'rtf', 'tex', 'txt', 'wpd', 'ppt', 'pptx', 'ppt', 'json', 'jfif'
        ];
        if (strpos($attribute, "file") !== false) {
            $pathInfo = pathinfo($value);
            if (!isset($pathInfo['extension'])) {
                $this->errormsg = $attribute . ' URL must end up with an extension.';
                return false;
            }
            if (!in_array($pathInfo['extension'], $extensions)) {
                $this->errormsg = $attribute . ' extension can be one of the following types: ' . implode(",", $extensions);
                return false;
            }
            try {
                $headers = get_headers($value, 1);
            } catch (\Exception $ex) {
                $this->errormsg = $attribute . ', Invalid file path.';
                return false;
            }
            if (isset($headers['Content-Length']) || isset($headers['content-length'])) {
                $filesize = empty($headers['Content-Length']) ? $headers['content-length'] : $headers['Content-Length'];
                if ($filesize > 2097152) {
                    $this->errormsg = $attribute . ' size must be less than 2 Mb';
                    return false;
                } else {
                    AttachmentRule::$overAllSize += $filesize;
                    if ((AttachmentRule::$overAllSize + BlobRule::$overAllSize) > (10 * 1048576)) {
                        $this->errormsg = 'over all size must be less than 10 Mb';
                        return false;
                    }
                }
            }
            if (isset($headers[0])) {
                if (!strpos($headers[0], '200')) {
                    $this->errormsg = $attribute . ', Invalid file path.';
                    return false;
                }
            }
            $basename = strtolower($pathInfo['basename']);
        }
        //  else if ($attribute == "file") {
        //     $fileExtension = $value->getClientOriginalExtension();
        //     $basename = $value->getClientOriginalName();
        //     if (!in_array($fileExtension, $extensions)) {
        //         $this->errormsg = 'File extension can be one of the following types: ' . implode(",", $extensions);
        //         return false;
        //     }
        // } else if ($attribute == "files") {
        //     foreach ($value as $filePath) {
        //         $pathInfo = pathinfo($filePath);
        //         if (!in_array($pathInfo['extension'], $extensions)) {
        //             $this->errormsg = 'File extension can be one of the following types: ' . implode(",", $extensions);
        //             return false;
        //         }
        //     }
        // }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errormsg;
    }
}
