<table class="table" width="100%">
	<tr>
		<td colspan="14"><hr></td>
	</tr>
	<tr>
		<td colspan="14" style="font-size:24px;"><strong>Report Data DPT</strong></td>
	</tr>
	<tr>
		<td colspan="2">Print On</td>
		<td colspan="4">: {{ date('Y-m-d H:i:s') }}</td>
	</tr>
    <tr>
		<td colspan="2">Print By</td>
		<td colspan="4">: {{ session('user_uniq_name') }}</td>
	</tr>
	<tr>
		<td colspan="6">&nbsp;</td>
	</tr>
</table>
<table style="border-color: black;" width="100%" cellspacing="0" cellpadding="1" border="1">
    <tr>
        <th>No</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>Alamat</th>
        <th>RT</th>
        <th>RW</th>
        <th>Provinsi</th>
        <th>Kabupaten</th>
        <th>Kecamatan</th>
        <th>Kelurahan</th>
        <th>TPS</th>
        <th>Dibuat Oleh</th>
        <th>Status</th>
    </tr>

    @php
    $i = 1;
    @endphp

    @if (!empty($data_dpt))
        @foreach ($data_dpt as $dpt)
            <tr role="row">
                <td height="80">{{ $i }}</td>
                <td height="80" style="mso-number-format:\@">{{ $dpt->dpt_nik ?? '-'}}</td>
                <td height="80">{{ $dpt->dpt_name ?? '-'}}</td>
                <td height="80">{{ $dpt->dpt_jenkel == 1 ? 'Laki-Laki' : 'Perempuan' }}</td>
                <td height="80">{{ $dpt->dpt_address ?? '-'}}</td>
                <td height="80" style="mso-number-format:\@">{{ $dpt->dpt_rt ?? '-'}}</td>
                <td height="80" style="mso-number-format:\@">{{ $dpt->dpt_rw ?? '-'}}</td>
                <td height="80">{{ $dpt->province->name ?? '-'}}</td>
                <td height="80">{{ $dpt->regency->name ?? '-'}}</td>
                <td height="80">{{ $dpt->district->name ?? '-'}}</td>
                <td height="80">{{ $dpt->village->name ?? '-'}}</td>

                @php
                $tps_code = $dpt->tps->tps_code ?? '-';
                $tps_name = $dpt->tps->tps_name ?? '-';
                @endphp

                <td height="80">{{ $tps_code .'-'. $tps_name; }}</td>
                <td height="80">{{ $dpt->role->role_name ?? '-'}}</td>
                <td height="80">{{ $dpt->dpt_status == 2 ? 'Active' : 'Deactive' }}</td>
            </tr>

            @php
            $i++;
            @endphp
        @endforeach
    @endif
</table>
