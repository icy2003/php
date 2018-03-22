<?php

namespace icy2003\ihelpers;

class Base64
{
    public static function file2Base64($file)
    {
        $base64 = null;
        if ($fp = fopen($file, 'rb', 0)) {
            $gambar = fread($fp, filesize($file));
            fclose($fp);

            $base64 = chunk_split(base64_encode($gambar));
        }

        return $base64;
    }

    public static function base64ImgTag($base64)
    {
        return '<img src="data:image/jpg/png/gif;base64,'.$base64.'" >';
    }
}
