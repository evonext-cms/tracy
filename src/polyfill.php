<?php

namespace EvoNext\Tracy
{
    if (function_exists('escapeshellarg') === true) {
        function escapeshellarg($input)
        {
            return \escapeshellarg($input);
        }
    } else {
        function escapeshellarg($input)
        {
            $input = str_replace('\'', '\\\'', $input);

            return '\''.$input.'\'';
        }
    }
}

namespace Tracy
{
    function escapeshellarg($input)
    {
        return \EvoNext\Tracy\escapeshellarg($input);
    }
}
