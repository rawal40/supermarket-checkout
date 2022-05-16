<?php

namespace App;

class Checkout implements CheckoutInterface
{
    /**
     * @var array
     */
    protected $cart = [];

    /**
     * @var int[]
     */
    protected $pricing = [
        'A' => 50,
        'B' => 30,
        'C' => 20,
        'D' => 15,
        'E' => 5
    ];

    /**
     * @var int[][]
     */
    protected $discounts = [
        'A' => [
            [
                'threshold' => 3,
                'amount' => 20
            ]
        ],
        'B' => [
            [
                'threshold' => 2,
                'amount' => 15
            ]
        ],
        'C' => [
            [
                'threshold' => 3,
                'amount' => 10
            ],
            [
                'threshold' => 2,
                'amount' => 2
            ],
        ],
        'D' => [
            [
                'withsku' => 'A',
                'threshold' => 1,
                'amount' => 10
            ]
        ],
    ];

    /**
     * @var int[]
     */
    protected $stats = [
        'A' => 0,
        'B' => 0,
        'C' => 0,
        'D' => 0,
        'E' => 0,
    ];

    /**
     * Adds an item to the checkout
     *
     * @param $sku string
     */
    public function scan(string $sku)
    {
        if (!array_key_exists($sku, $this->pricing)) {
            return;
        }

        $this->stats[$sku] = $this->stats[$sku] + 1;

        $this->cart[] = [
            'sku' => $sku,
            'price' => $this->pricing[$sku]
        ];
    }

    /**
     * Calculates the total price of all items in this checkout
     *
     * @return int
     */
    public function total(): int
    {
        $standardPrices = array_reduce($this->cart, function ($total, array $product) {
            $total += $product['price'];
            return $total;
        }) ?? 0;

        $totalDiscount = 0;

        foreach ($this->discounts as $key => $discounts) {
            $numberOfItems = $this->stats[$key];
            foreach ($discounts as $discount) {
                if ($numberOfItems >= $discount['threshold']) {
                    $withsku = $discount['withsku'] ?? '';
                    if ($withsku && $this->stats[$withsku]) {
                        $numberOfItems = ($this->stats[$withsku] <= $numberOfItems) ? $this->stats[$withsku] : $numberOfItems;
                    }
                    $numberOfSets = floor($numberOfItems / $discount['threshold']);
                    $totalDiscount += ($discount['amount'] * $numberOfSets);
                    $numberOfItems = $numberOfItems - $numberOfSets * $discount['threshold'];
                }
            }
        }

        return $standardPrices - $totalDiscount;
    }
}
