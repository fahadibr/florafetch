<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use League\Csv\Reader;

class CsvImportService
{
    public function import(string $filePath): array
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $results = ['imported' => 0, 'errors' => []];

        foreach ($csv->getRecords() as $offset => $record) {
            $row = $offset + 2; // 1-indexed, header is row 1
            $errors = $this->validateRow($record);

            if (!empty($errors)) {
                $results['errors'][] = "Row {$row}: " . implode(', ', $errors);
                continue;
            }

            $category = Category::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($record['category'])],
                ['name' => $record['category']]
            );

            Product::create([
                'category_id'          => $category->id,
                'common_name'          => $record['common_name'],
                'botanical_name'       => $record['botanical_name'],
                'price'                => (float) $record['price'],
                'size'                 => $record['size'],
                'stock_quantity'       => (int) ($record['stock_quantity'] ?? 0),
                'sunlight_requirement' => $record['sunlight_requirement'],
                'watering_frequency'   => $record['watering_frequency'],
                'is_low_maintenance'   => filter_var($record['is_low_maintenance'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_pet_friendly'      => filter_var($record['is_pet_friendly'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'growth_rate'          => $record['growth_rate'] ?? null,
            ]);

            $results['imported']++;
        }

        return $results;
    }

    private function validateRow(array $record): array
    {
        $errors = [];
        $required = ['common_name', 'botanical_name', 'price', 'size', 'category', 'sunlight_requirement', 'watering_frequency'];

        foreach ($required as $field) {
            if (empty($record[$field])) {
                $errors[] = "missing {$field}";
            }
        }

        if (!empty($record['price']) && (float) $record['price'] <= 0) {
            $errors[] = 'price must be greater than 0';
        }

        if (!empty($record['size']) && !in_array($record['size'], ['small', 'medium', 'large'])) {
            $errors[] = 'size must be small, medium, or large';
        }

        if (!empty($record['growth_rate']) && !in_array($record['growth_rate'], ['Slow', 'Moderate', 'Fast'])) {
            $errors[] = 'growth_rate must be Slow, Moderate, or Fast';
        }

        return $errors;
    }
}
