<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use Filament\Actions\ImportAction;
use App\Imports\CustomerImporter;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
            ->importer(CustomerImporter::class)
            ->chunkSize(100) // İhtiyaca göre ayarlayın
            ->maxRows(1000), // İzin verilen maksimum satır sayısı

                // ->uniqueField('email')
                // ->fields([
    //     ImportField::make('company_name')
    //         ->required(),
    //     ImportField::make('email')
    //         ->required(),
    //     ImportField::make('phone')
    //         ->required(),
    //     ImportField::make('account_name')
    //         ->required(),
    //     ImportField::make('account_start_date')
    //         ->required()
    //         ->date(),
    //     ImportField::make('notification_date')
    //         ->required()
    //         ->date(),
    //     ImportField::make('status')
    //         ->required()
    //         ->options([
    //             'active' => 'Active',
    //             'renewal' => 'Renewal',
    //             'archived' => 'Archived',
    //         ]),
    //     ImportField::make('product_id')
    //         ->required()
    //         ->relationship('product', 'name'),
    // ]),
            ExportAction::make()
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                        ->withColumns([
                            Column::make('updated_at'),
                        ])
                ]),
        ];
    }
}
