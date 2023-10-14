<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style type="text/css">
        @page { 
            margin-top: 10px;
            margin-bottom: 5px;
        }
        body {
            font-size: 9px;
        }
        table {
            table-layout: fixed;
            word-wrap: break-word;
        }
        tr td {
            padding: 0 !important;
            margin: 0 !important;
        }
        input[type=checkbox]:checked {
            font-weight:bold;
            color:black;
        }
    </style>
    <title>Print Visit Order {{ $visit_order->visit_order_number }}</title>
</head>
<body>
    {{-- Barisan Awal --}}
    <table class='table table-borderless mb-1'>
        <tbody>
            <tr>
                <td colspan="3">No: {{ $visit_order->visit_order_number }}</td>
                <td colspan="3" style="text-align: right">Lampiran 3</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center"><strong>Laporan Site Visit</strong></td>
            </tr>
        </tbody>
    </table>

    {{-- Data Tabel --}}
    <table class='table table-borderless my-0'>
        <tbody style="border: 1px solid;">
            <tr>
                <td colspan="2">Nama Debitur</td>
                <td colspan="4" style="border-right: 1px solid;">: {{ $visit_order->debtor->debtor_name }}</td>
                <td colspan="2">Tanggal Peninjauan</td>
                <td colspan="4">: {{ $latest_visit_order_history }}</td>
            </tr>
            <tr>
                <td colspan="2">Cabang Pemohon {{ ($visit_order->visit_order_custom_number) ? '& Kode' : '' }}</td>
                <td colspan="4" style="border-right: 1px solid;">: {{ $visit_order->site->site_name.(($visit_order->visit_order_custom_number) ? '('.$visit_order->visit_order_custom_number.')' : '') }}</td>
                <td colspan="2">Nama AO</td>
                <td colspan="4">: {{$visit_order->site_contact->site_contact_fullname}}</td>
            </tr>
            <tr>
                <td colspan="2">Lokasi/Alamat Agunan</td>
                <td colspan="4" style="border-right: 1px solid;">: {{ $visit_order->visit_order_location }}</td>
                <td colspan="2">Foto </td>
                <td colspan="4">:</td>
            </tr>
        </tbody>
        <tbody style="border: 1px solid;">
            @if ($visit_order->visit_order_visual->count() > 3)
                @foreach ($visit_order->visit_order_visual->chunk(4) as $listVisitOrderVisual)
                <tr style="border: 1px solid;">
                    @foreach ($listVisitOrderVisual as $item)
                    <td colspan="3" style="border: 1px solid;text-align: center;">
                        @if (file_exists(public_path('storage/visit_order_visual_uploads/'.$date.'/'.$item->visit_order_visual_file)))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/visit_order_visual_uploads/'.$date.'/'.$item->visit_order_visual_file))) }}" alt="" style="width: 100%;height: 25%;">
                        <p style="margin: 0%">{{$item->visit_order_visual_file_name}}</p>                        
                        @else
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/no-image-asset.jpg'))) }}" alt="" style="width: 100%;height: 25%;">
                        <p style="margin: 0%">{{$item->visit_order_visual_file_name}}</p>                        
                        @endif 
                    </td>
                    @endforeach
                    @for ($i = 4; $i > $listVisitOrderVisual->count(); $i--)
                    <td colspan="3"></td>
                    @endfor
                </tr>
                @endforeach
            @else
                @foreach ($visit_order->visit_order_visual->chunk(3) as $listVisitOrderVisual)
                <tr style="border: 1px solid;">
                    @foreach ($listVisitOrderVisual as $item)
                    <td colspan="4" style="border: 1px solid;text-align: center;">
                        @if (file_exists(public_path('storage/visit_order_visual_uploads/'.$date.'/'.$item->visit_order_visual_file)))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/visit_order_visual_uploads/'.$date.'/'.$item->visit_order_visual_file))) }}" alt="" style="width: 100%;height: 25%;">
                        <p style="margin: 0%">{{$item->visit_order_visual_file_name}}</p>                        
                        @else
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/no-image-asset.jpg'))) }}" alt="" style="width: 100%;height: 25%;">
                        <p style="margin: 0%">{{$item->visit_order_visual_file_name}}</p>                        
                        @endif 
                    </td>
                    @endforeach
                    @for ($i = 3; $i > $listVisitOrderVisual->count(); $i--)
                    <td colspan="4"></td>
                    @endfor
                </tr>
                @endforeach
            @endif
            <tr>
                <td colspan="12">Keterangan: </td>
            </tr>
            @php
                $no = 1;
            @endphp
            @foreach ($checklistGroup as $group)
            <tr style="border: 1px solid;">
                <td colspan="12">{{ $no.'. '.$group->checklist_group_name }}</td>
            </tr>
            @if (12 % $group->checklist_active->count() != 0 || $group->checklist_active->count() == 12)
                @foreach ($group->checklist_active->chunk(6) as $checklistChunk)
                <tr>
                    @foreach($checklistChunk as $checklist)
                    @php
                        $isSame = false;
                        $textAnswer = '';  
                    @endphp
                    @foreach ($checklistAnswer as $answer)
                        @if ($checklist->checklist_id == $answer->checklist_id)
                            {{$isSame = true}}
                            {{($checklist->checklist_is_freetext == 2) ? $textAnswer = $answer->checklist_answer : $textAnswer = ''}}
                        @endif
                    @endforeach
                    <td colspan="2" class="{{($isSame) ? 'font-weight-bold' : ''}}"><span style="font-family: DejaVu Sans; sans-serif;font-size:14px">{{ (($isSame) ? '☑' : '☐') }}</span> {{ $checklist->checklist_name.(($textAnswer != '') ? ': '.$textAnswer : '') }}</td>
                    @endforeach
                    @for ($i = 6; $i > $checklistChunk->count(); $i--)
                    <td colspan="2"></td>
                    @endfor
                </tr>
                @endforeach

                @php
                    $no++;
                @endphp
            @else
                @php
                    $col = 12 / $group->checklist_active->count();
                @endphp
                @foreach ($group->checklist_active->chunk($group->checklist_active->count()) as $checklistChunk)
                <tr>
                    @foreach($checklistChunk as $checklist)
                    @php
                        $isSame = false;
                        $textAnswer = '';  
                    @endphp
                    @foreach ($checklistAnswer as $answer)
                        @if ($checklist->checklist_id == $answer->checklist_id)
                            {{$isSame = true}}
                            {{($checklist->checklist_is_freetext == 2) ? $textAnswer = $answer->checklist_answer : $textAnswer = ''}}
                        @endif
                    @endforeach
                    <td colspan="{{ $col }}" class="{{($isSame) ? 'font-weight-bold' : ''}}"><span style="font-family: DejaVu Sans; sans-serif;font-size:14px">{{ (($isSame) ? '☑' : '☐') }}</span> {{ $checklist->checklist_name.(($textAnswer != '') ? ': '.$textAnswer : '') }}</td>
                    @endforeach
                    @for ($i = $group->checklist_active->count(); $i > $checklistChunk->count(); $i--)
                    <td colspan="{{ $col }}"></td>
                    @endfor
                </tr>
                @endforeach

                @php
                    $no++;
                @endphp
            @endif
            
            @endforeach
        </tbody>
    </table>

    {{-- Data Tanda Tangan --}}
    <table class='table table-borderless mb-0' style="text-align: center;">
        <tbody>
            <tr>
                <td colspan="2">
                    <p><strong> Surveyor </strong></p>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/no-image-asset.jpg'))) }}" style="visibility: hidden" alt="" width="25" height="25">
                    <p>{{ session('user_uniq_name') }}</p>
                </td>
                <td colspan="2">
                    <p><strong> Account Manager </strong></p>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/no-image-asset.jpg'))) }}" style="visibility: hidden" alt="" width="25" height="25">
                    <p>Steve</p>
                </td>
                <td colspan="2">
                    <p><strong> Account Officer </strong></p>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/no-image-asset.jpg'))) }}" style="visibility: hidden" alt="" width="25" height="25">
                    <p>{{$visit_order->site_contact->site_contact_fullname}}</p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>