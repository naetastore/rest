<?php

function rupiah_format($number)
{
    $decimal="0";
    $decimal_separator=",";
    $thousand_separator=".";
    return number_format($number, $decimal, $decimal_separator, $thousand_separator);
}
