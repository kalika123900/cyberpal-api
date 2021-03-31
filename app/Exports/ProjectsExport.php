<?php

namespace App\Exports;

use App\Projects;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use \PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProjectsExport implements FromArray, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function array(): array
    {    
        $projects = Projects::with(['category', 'user', 'merchant'])->orderBy('updated_at', 'DESC')->get()->toArray();
        return $projects;
    }
  
    /**
    * @var Invoice $invoice
    */
    public function map($projects): array
    {
        return [
            $projects['id'] ?? '-',
            $projects['reference_id'] ?? '-',
            $projects['status'] ?? '-',
            $projects['service_type'] ?? '-',
            $projects['estimated_setup_date'] ??  '-',
            $projects['budget'] ?? '-',
            $projects['project_type'] ?? '-',
            $projects['job_type'] ?? '-',
            $projects['skills_required'] ?? '-',
            $projects['min_experience'] ?? '-',
            $projects['language_preference'] ?? '-',
            $projects['project_timeline'] ?? '-',
            $projects['website'] ?? '-',
            $projects['message']  ?? '-',
            $projects['category']['name']  ?? '-',
            $projects['user']['name']  ?? '-',
            $projects['user']['email']  ?? '-',
            $projects['user']['phone']  ?? '-',
            $projects['merchant']['name']  ?? 'Not Assigned yet',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Reference ID',
            'Status',
            // 'Generated At',
            'Service Type',
            'Estimated Setup Date',
            'Budget',
            'Project Type',
            'Job Type',
            'Skills Required',
            'Minimum Experience',
            'Language Preference',
            'Project Timeline',
            'Website',
            'Message',
            'Category',
            'User Name',
            'User Email',
            'User Phone',
            'Assigned Merchant',
        ];
    }
}
