<?php

return [

    'navigation_label' => 'Invoices',
    'model_label' => 'Invoice',
    'plural_model_label' => 'Invoices',

    'sections' => [
        'details' => 'Invoice Details',
        'line_items' => 'Line Items',
        'additional_costs' => 'Additional Costs',
        'total' => 'Total',
    ],

    'fields' => [
        'customer_name' => 'Customer Name',
        'date' => 'Date',
        'items' => 'Items',
        'description' => 'Description',
        'quantity' => 'Quantity',
        'length' => 'Length',
        'width' => 'Width',
        'area' => 'Area',
        'unit_price' => 'Unit Price',
        'line_total' => 'Line Total',
        'shipping_cost' => 'Shipping Cost',
        'installation_cost' => 'Installation Cost',
        'grand_total' => 'Grand Total',
    ],

    'actions' => [
        'add_item' => 'Add Item',
    ],

    'columns' => [
        'customer' => 'Customer',
        'date' => 'Date',
        'items' => 'Items',
        'shipping' => 'Shipping',
        'installation' => 'Installation',
        'grand_total' => 'Grand Total',
    ],

];
