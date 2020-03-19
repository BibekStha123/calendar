<?php

namespace bibek\calendar\helpers;

class NepaliHelper
{       
    public static function englishToNepali($input){
        $standard_numsets = array("0","1","2","3","4","5","6","7","8","9",".");
        $devanagari_numsets = array("०","१","२","३","४","५","६","७","८","९","|");
        
        return str_replace($standard_numsets, $devanagari_numsets, $input);
    }
}