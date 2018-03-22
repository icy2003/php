<?php

function image($file)
{
    if ($fp = fopen($file, 'rb', 0)) {
        $gambar = fread($fp, filesize($file));
        fclose($fp);

        $base64 = chunk_split(base64_encode($gambar));
        echo '<img src="data:image/jpg/png/gif;base64,'.$base64.'" >';
    }
}