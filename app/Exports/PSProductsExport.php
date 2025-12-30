<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Storage;


class PSProductsExport implements FromCollection, WithHeadings, WithMapping, WithDrawings, WithEvents {

    private $products;
    private $imageHeight = 50; // Set a default image height

    public function __construct($products) {
        $this->products = collect($products); // Convert array to Collection
    }

    public function collection() {
        return $this->products;
    }

    public function headings(): array {
        return ['Image URL', 'OEM P/N', 'Description','Title', 'Brand', 'Models',
            'PS Price', 'Price IMED', 'Price IMED Ref',
            'Condition'];
    }

    public function map($row): array {
        return [
            $this->getImage($row),
            $row['partNumber'] ?? '',
            $row['description'] ?? '',
            $row['title'] ?? '',
            $row['brand'] ?? '',
            $row['models'] ?? '',
            '$' . ($row['options'][0]['price'] ?? ''),
            isset($row['options'][0]['price']) ? '$' . round($row['options'][0]['price'] - ($row['options'][0]['price'] * 15 / 100), 2) : '',
            isset($row['options'][0]['price']) ? '$' . round($row['options'][0]['price'] - ($row['options'][0]['price'] * 35 / 100), 2) : '',
            isset($row['options'][0]['lineItemCondition']) ? $this->getCondition($row['options'][0]['lineItemCondition']) : '',
            
        ];
    }

    private function getCondition($value) {
        if ($value == 1) {
            return "New OEM Original";
        } else if ($value == 6) {
            return "Refurbished";
        } else if ($value == 2) {
            return "New Aftermarket";
        }
    }
    
    private function getImage($row){
        if (!empty($row['thumbnailUrl'])) {
                // Download image locally
                $imagePath = storage_path('app/public/temp_image_' . $row['partNumber'] . '.png');
                file_put_contents($imagePath, file_get_contents($row['thumbnailUrl']));
                return Storage::disk('public')->url('temp_image_' . $row['partNumber'] . '.png');
            }
            return '';
    }

    public function drawings() {
        $drawings = [];
        /*foreach ($this->products as $index => $row) {
            if (!empty($row['thumbnailUrl'])) {
                $drawing = new Drawing();
                $drawing->setName('Product Image');
                $drawing->setDescription('Product Image');

                // Download image locally
                $imagePath = storage_path('app/public/temp_image_' . $row['partNumber'] . '.png');
                file_put_contents($imagePath, file_get_contents($row['thumbnailUrl']));

                $drawing->setPath($imagePath);
                $drawing->setHeight($this->imageHeight); // Set image height
                $drawing->setCoordinates('A' . ($index + 2)); // Position image in correct row

                $drawings[] = $drawing;
            }
        }*/

        return $drawings;
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = $this->products->count();

                //set the row height equal to image height
                for ($i = 2; $i <= $rowCount + 1; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight($this->imageHeight);
                }
            },
        ];
    }

}
