<?php

namespace App\Imports;

use App\Models\DataDpt;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;

class DptImport implements ToModel, WithHeadingRow, WithUpserts, WithUpsertColumns, WithValidation
{

    use Importable;

    public function uniqueBy()
    {
        return 'dpt_nik';
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new DataDpt([
            'dpt_status'        => $row['dpt_status'],
            'dpt_nik'           => $row['dpt_nik'],
            // 'dpt_nik' => [
            //     'required',
            //     Rule::unique('sys_dpt', 'dpt_nik')->where('dpt_status', request()->input('dpt_status')),
            //     'digits:16', // Added validation for 16-digit NIK
            // ],
            'dpt_name'          => $row['dpt_name'],
            'dpt_jenkel'        => $row['dpt_jenkel'],
            'dpt_address'       => $row['dpt_address'],
            'dpt_rt'            => $row['dpt_rt'],
            'dpt_rw'            => $row['dpt_rw'],
            'tps_id'            => $row['tps_id'],
            'dpt_province'      => $row['dpt_province'],
            'dpt_regency'       => $row['dpt_regency'],
            'dpt_district'      => $row['dpt_district'],
            'dpt_village'       => $row['dpt_village'],
            'role_id'           => session('role_id'),
            'dpt_created_by'    => session('user_id'),
            'dpt_created_date'  => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    public function rules(): array
    {
        return [
            'dpt_status'    => 'required',
            'dpt_nik'       => 'required|unique:sys_dpt,dpt_nik',
            'dpt_name'      => 'required',
            'dpt_jenkel'    => 'required',
            'dpt_address'   => 'required',
            'dpt_rt'        => 'required',
            'dpt_rw'        => 'required',
            // 'tps_id'        => 'required',
            'dpt_province'  => 'required',
            'dpt_regency'   => 'required',
            'dpt_district'  => 'required',
            'dpt_village'   => 'required'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'dpt_nik.unique' => ':attribute already exists.',
            'dpt_nik.digits' => ':attribute must be exactly 16 digits.',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function upserts(): array
    {
        return [
            DataDpt::class => [
                'dpt_status' => '=',
            ],
        ];
    }

    public function upsertColumns(): array
    {
        return [
            'dpt_village' => '=',
        ];
    }
}
