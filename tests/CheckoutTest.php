<?php

namespace Tests;

use App\Checkout;
use PHPUnit\Framework\TestCase;

class CheckoutTest extends TestCase
{
    public function test_scanning_sku_a_returns_total_of_50()
    {
        $checkout = new Checkout();

        $checkout->scan('A');

        $this->assertEquals(50, $checkout->total(), 'Checkout total does not equal expected value of 50');
    }

    public function test_an_empty_checkout_returns_a_total_of_zero()
    {
        $checkout = new Checkout();

        $this->assertEquals(0, $checkout->total(), 'Checkout total does not equal expected value of 0');
    }

    public function test_adding_an_invalid_sku_does_not_crash_the_checkout()
    {
        $checkout = new Checkout();

        $checkout->scan('WANDAVISION!');

        $this->assertEquals(0, $checkout->total(), 'Checkout total does not equal expected value of 0');
    }

    /**
     * @dataProvider basketProvider
     */
    public function test_scanning_multiple_skus_returns_the_expected_totals($expectedTotal, $itemsToAdd)
    {
        $checkout = new Checkout();

        array_map(function (string $sku) use ($checkout) {
            $checkout->scan($sku);
        }, $itemsToAdd);

        $this->assertEquals(
            $expectedTotal,
            $checkout->total(),
            'Checkout total does not equal expected value of ' . $expectedTotal
        );
    }

    /**
     * Provide various baskets full of items along with what the total of these items should be
     *
     * @return array
     */
    public function basketProvider()
    {
        return [
            [100, ['A', 'A']],
            [130, ['A', 'A', 'A']],
            [130, ['A', 'A', 'B']],
            [85, ['A', 'C', 'D']],
        ];
    }

    public function test_the_price_is_discounted_when_ordering_three_times_a()
    {
        $checkout = new Checkout();

        $checkout->scan('A');
        $checkout->scan('A');
        $checkout->scan('A');

        $this->assertEquals(130, $checkout->total(), 'Checkout total does not equal expected value of 130');
    }

    public function test_the_price_is_discounted_multiple_times_when_ordering_six_times_a()
    {
        $checkout = new Checkout();

        $checkout->scan('A');
        $checkout->scan('A');
        $checkout->scan('A');

        $checkout->scan('A');
        $checkout->scan('A');
        $checkout->scan('A');

        $this->assertEquals(260, $checkout->total(), 'Checkout total does not equal expected value of 260');
    }

    public function test_the_price_is_discounted_multiple_times_when_ordering_seven_times_a()
    {
        $checkout = new Checkout();

        $checkout->scan('A');
        $checkout->scan('A');
        $checkout->scan('A');

        $checkout->scan('A');
        $checkout->scan('A');
        $checkout->scan('A');

        $checkout->scan('A');

        $this->assertEquals(310, $checkout->total(), 'Checkout total does not equal expected value of 310');
    }

    public function test_the_price_is_discounted_when_ordering_two_times_b()
    {
        $checkout = new Checkout();

        $checkout->scan('B');
        $checkout->scan('B');

        $this->assertEquals(45, $checkout->total(), 'Checkout total does not equal expected value of 45');
    }
}
