<?php

namespace App\Traits;

trait InvoiceCalculations
{
    /**
     * محاسبه مساحت، متراژ و قیمت هر ردیف
     */
    protected function calculateRow($width, $height, $quantity, $unitPrice)
    {
        if ($width <= 0 || $height <= 0 || $quantity <= 0 || $unitPrice <= 0) {
            return [
                'area' => 0,
                'meterage' => 0,
                'total_price' => 0
            ];
        }

        // مساحت = (عرض * ارتفاع) / 10000 (تبدیل به مترمربع)
        $area = ($width * $height) / 10000;

        // متراژ = حداقل 0.8 یا مساحت × تعداد
        $meterage = ($area < 0.8 ? 0.8 : $area) * $quantity;

        // قیمت توری = قیمت واحد × متراژ
        $totalPrice = $unitPrice * $meterage;

        return [
            'area' => round($area, 4),
            'meterage' => round($meterage, 4),
            'total_price' => round($totalPrice, 2)
        ];
    }

    /**
     * محاسبه کل فاکتور
     */
    protected function calculateGrandTotal($items, $shippingCost, $installationCost)
    {
        $sumTotal = 0;

        foreach ($items as &$item) {
            $result = $this->calculateRow(
                (float) ($item['width'] ?? 0),
                (float) ($item['height'] ?? 0),
                (float) ($item['quantity'] ?? 0),
                (float) ($item['unit_price'] ?? 0)
            );

            $item['area'] = $result['area'];
            $item['meterage'] = $result['meterage'];
            $item['total_price'] = $result['total_price'];

            $sumTotal += $result['total_price'];
        }

        return [
            'items' => $items,
            'grand_total' => $sumTotal + (float) $shippingCost + (float) $installationCost
        ];
    }

    /**
     * فرمت عدد با جداکننده هزارگان (برای نمایش)
     */
    protected function formatNumber($value, $decimals = 0)
    {
        if ($decimals > 0) {
            return number_format($value, $decimals, '.', ',');
        }
        return number_format($value, 0, '.', ',');
    }

    /**
     * تبدیل عدد فرمت شده به float
     */
    protected function parseNumber($value)
    {
        if (!$value) return 0;
        return (float) str_replace(',', '', $value);
    }
}
