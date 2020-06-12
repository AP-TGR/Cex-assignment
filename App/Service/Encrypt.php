<?php

namespace App\Service;

class Encrypt
{
    /**
     * The original string to encrypt the string
     *
     * @var string
     */
    private $_originalString = "abcdefghijklmnopqrstuvwxyz";

    /**
     * Returns encrypted string
     *
     * @param string $string
     * @return string
     */
    public function encrypt($string)
    {
        $encryptedString = '';

        // Get the rotated string
        $rotatedString = $this->getRotatedString($string);

        // Loop through the given string and find the specific charcter based on position
        foreach(str_split($string) as $char) {
            $encryptedString .= $rotatedString[strpos($this->_originalString, $char)];
        }

        return $encryptedString;
    }

    /**
     * Returns rotated string
     *
     * @param string $string
     * @return void
     */
    public function getRotatedString($string)
    {
        $strLen = strlen($string);
        return substr($this->_originalString, $strLen) . substr($this->_originalString, 0, $strLen);
    }
}