<?php

declare(strict_types=1);

namespace App\Service;

class CartService
{

    /**
     * merge two tables
     *
     * @param array<{string:int}> $cart1 ['key' => 124, 'keyprim' => 1]
     * @param array<{string:int}> $cart2 ['key' => 4, 'key1' => 1]
     * @return array<{string:int}>
     */
    public function mergeCartsAfterLogin(array $cart1, array $cart2): array
    {
        $totalCart = [];

        foreach (array_merge_recursive($cart1, $cart2) as $key => $value) {
            if (is_array($value)) {
               // 
               // [
               //    0 => 2, // value of session
               //    1 => 5  // value of database
               // ]
               //
                $totalCart[$key] = array_sum($value); // $totalCart[$key] = 7;
            }else {
                // 6 value from the database or the session
                $totalCart[$key] = $value; // $totalCart[$key] = 6;
            }
        }
    
        return $totalCart;
    }

}