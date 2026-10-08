<?php

namespace App\Exports;

use App\Exports\Sheets\AccountGroupReferenceSheet;
use App\Exports\Sheets\CustomerClassReferenceSheet;
use App\Exports\Sheets\CustomerImportTemplateSheet;
use App\Exports\Sheets\CustomerInstructionsSheet;
use App\Exports\Sheets\SalesReferenceSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CustomerImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new CustomerImportTemplateSheet(),     // Sheet 1: Template data import
            new CustomerClassReferenceSheet(),     // Sheet 2: Reference Customer Class
            new AccountGroupReferenceSheet(),      // Sheet 3: Reference Account Group
            new CustomerInstructionsSheet(),       // Sheet 4: Penjelasan Data & Customer Type
            new SalesReferenceSheet(),             // Sheet 5: Reference Sales (User ID)
        ];
    }
}
