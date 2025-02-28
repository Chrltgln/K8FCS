<?php
function generateTransactionID()
{
    // Characters to be used in the transaction ID
    $characters = '0123456789';
    $charactersLength = strlen($characters);

    $part1 = '';
    for ($i = 0; $i < 6; $i++) {
        $part1 .= $characters[rand(0, $charactersLength - 1)];
    }

    $part2 = '';
    for ($i = 0; $i < 5; $i++) {
        $part2 .= $characters[rand(0, $charactersLength - 1)];
    }

    // Format and return the transaction ID
    return '2024-' . $part1 . '-' . $part2;
}
?>
