<?php

namespace App\Exports;

use App\Leads;
use App\Solution;
use App\Resellers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use \PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SingleLeadExport implements FromArray, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;
        
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function array(): array
    {    
        $data = Leads::where('id', $this->id)->with(['location', 'merchant'])->orderBy('updated_at', 'DESC')->get();
        $leads = [];

        foreach ($data as $lead) {
            $data = '';

            if (!empty ($lead->requestedResellers)) {
                if ($lead->fromWhere === "solution-direct" || $lead->fromWhere === "solutions-search") {
                    foreach ($lead->requestedResellers as $id) {
                        $solution = Solution::where('id', $id)->first();
                        if (!empty($solution)) {
                            $data = $data . ", " . $solution->title;
                        }
                    }
                } else if ($lead->fromWhere === "resellers") {
                    foreach ($lead->requestedResellers as $id) {
                        $reseller = Resellers::where('id', $id)->first();
                        if (!empty ($reseller)) {
                            $data = $data . ", " . $reseller->name;
                        }
                    }
                }
            }
            
            $lead['requestedServices'] = $data;
            array_push($leads, $lead);
        }

        return $leads;
    }
  
    /**
    * @var Invoice $invoice
    */
    public function map($leads): array
    {
        return [
            $leads->id,
            Date::dateTimeToExcel($leads->created_at),
            $leads->fromWhere ?? '-',
            $leads->status ?? '-',
            $leads->full_name ?? '-',
            $leads->phone ?? '-',
            $leads->email ?? '-',
            $leads->organisation_name ?? '-',
            $leads->organisation_url ?? '-',
            $leads->organisational_role ?? '-',
            $leads->organisation_size ?? '-',
            $leads->industry ?? '-',
            $leads->budget ?? '-',
            $leads->implementation_time_period ?? '-',
            $leads->open_emerging_vendors ?? '-',
            $leads->requirement_type ?? '-',
            $leads->user_id ?? '-',
            // $leads->location ? $leads->location->postcode_sector : "-",
            $leads->current_solution ?? '-',
            $leads->solution_type ?? '-',
            $leads->reseller_type ?? '-',
            $leads->merchant->business_name ?? '-',
            $leads->merchant_lead_status ?? '-',
            $leads->message ?? '-',
            $leads->requestedServices ?? '-',
            $leads->requestNeeded ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Generated At',
            'Generated From',
            'Status',
            'Full Name',
            'Phone',
            'Email Address',
            'organisation_name',
            'organisation_url',
            'organisational_role',
            'organisation_size',
            'Industry',
            'Budget',
            'Estimated Setup Date',
            'Include Emerging Vendors',
            'Requirement Type',
            'loggedin User Id',
            'PostCode Sector',
            'Current Solution',
            'Solution Type',
            'Reseller Type',
            'Assigned Merchant',
            'Merchant Status',
            'Message',
            'Requested Services',
            'Request Needed',
        ];
    }
}
