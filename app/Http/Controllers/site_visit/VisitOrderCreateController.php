<?php

namespace App\Http\Controllers\site_visit;

use Carbon\Carbon;
use App\Models\VisitOrder;
use Illuminate\Http\Request;
use App\Mail\VisitOrderEmail;
use App\Http\Controllers\Controller;
use App\Mail\VisitOrderEmailClient;
use App\Mail\VisitOrderEmailPartner;
use App\Models\Debtor;
use App\Models\MasterDistricts;
use App\Models\MasterProvinces;
use App\Models\MasterRegencies;
use App\Models\MasterVillages;
use App\Models\SiteContact;
use App\Models\VisitOrderHistory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VisitOrderCreateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (session('role_id') == 1 || session('role_id') == 2) {
            return view('content.site-visit.visit-order-create', [
                'code_number' => $this->generateUniqueCode('WO'),
            ]);
        } else {
            return view('content.pages.pages-misc-not-authorized');
        }
    }

    /**
     * Generate a unique code based on the given prefix and year/month of creation.
     * The code will be incremented automatically based on the latest code in the database.
     *
     * @param string $prefix The prefix for the code, e.g. WO.
     * @return string The generated code.
     */
    private function generateUniqueCode($prefix)
    {
        $currentYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        // Get the last visit order for the current month
        $lastVisitOrder = VisitOrder::where('visit_order_number', 'LIKE', "$prefix/$currentYear/$currentMonth/%")
            ->orderBy('visit_order_id', 'desc')
            ->first();

        if ($lastVisitOrder) {
            // Extract the last number from the last visit order in the current month
            $lastNumber = intval(substr($lastVisitOrder->visit_order_number, -5));

            // Check if it's still the same month
            if ($lastVisitOrder->visit_order_created_date->format('m') == $currentMonth) {
                $newNumber = $lastNumber + 1;
            } else {
                // If it's a new month, start with 00001
                $newNumber = 1;
            }
        } else {
            // If there are no previous visit orders for the current month, start with 00001
            $newNumber = 1;
        }

        $formattedNumber = sprintf("%05d", $newNumber);
        return $prefix . '/' . $currentYear . '/' . $currentMonth . '/' . $formattedNumber;
    }

    /**
     * save data to sys_visit_order_history
     * 
     */
    private function visit_order_history($visitOrderId, $historyDesc, $createBy, $status)
    {
        $visit_order_history = VisitOrderHistory::create([
            'visit_order_id'                   => $visitOrderId,
            'visit_order_status'               => $status,
            'visit_order_history_desc'         => $historyDesc,
            'visit_order_history_created_by'   => $createBy,
            'visit_order_history_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $visit_order_history->save();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id'                 => 'required',
            'site_id'                   => 'required',
            'site_contact_id'           => 'required',
            'debtor_id'                 => 'required',
            'visit_type_id'             => 'required',
            'visit_order_number'        => 'required',
            'visit_order_location'      => 'required|max:255',
            'visit_order_date'          => 'required',
        ]);

        //   dd($request->all());
        // dd($validator->errors());

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }

        // panggil fungsi generateUniqueCode() untuk membuat visit_order_number baru
        $visitOrderNumber = $this->generateUniqueCode('WO');

        $status = $request->filled('partner_id') ? 2 : 1;

        VisitOrder::create([
            'visit_order_status'        => $status,
            'client_id'                 => $request->client_id,
            'site_id'                   => $request->site_id,
            'site_contact_id'           => $request->site_contact_id,
            'debtor_id'                 => $request->debtor_id,
            'visit_type_id'             => $request->visit_type_id,
            'partner_id'                => $request->partner_id,
            'visit_order_number'        => $visitOrderNumber,
            'visit_order_custom_number'        => $request->visit_order_custom_number,
            'visit_order_location'      => $request->visit_order_location,
            'visit_order_location_map'  => $request->visit_order_location_map,
            'visit_order_latitude'      => $request->visit_order_latitude,
            'visit_order_longitude'     => $request->visit_order_longitude,
            'visit_order_date'          => $request->visit_order_date,
            'visit_order_due_date'      => $request->visit_order_due_date,
            'visit_order_province'      => $request->visit_order_province,
            'visit_order_regency'       => $request->visit_order_regency,
            'visit_order_district'      => $request->visit_order_district,
            'visit_order_village'       => $request->visit_order_village,
            'visit_order_note'          => $request->visit_order_note,
            'visit_order_created_by'    => session('user_id'),
            'visit_order_created_date'  => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        // Mengambil alamat email PIC (person in charge) client 
        $visitOrder  = VisitOrder::latest()->first(); // mengambil data terakhir yang disimpan.
        $clientEmail = $visitOrder->client->client_email;
        $clientName  = $visitOrder->client->client_name;
        // ini utk partner
        $partnerEmail = null;
        $partnerName = null;
        if ($visitOrder->partner) {
            $partnerEmail = $visitOrder->partner->partner_email;
            $partnerName = $visitOrder->partner->partner_name;
        }
        $visit_order_number  = $visitOrderNumber;
        $debtor_name         = $visitOrder->debtor->debtor_name;
        $location            = $visitOrder->visit_order_location;
        $due_date            = format_short_local_date($visitOrder->visit_order_due_date);

        // Periksa apakah email PIC client & partner tersedia sebelum mengirim email
        if ($clientEmail || $partnerEmail) {
            if ($request->filled('partner_id')) { // cek juga jika status itu 2 = assigned
                $subject = 'DPI Site Visit Order - ' . $visitOrderNumber;
                Mail::to($clientEmail)->send(new VisitOrderEmailClient($subject, $clientName));
                if ($partnerEmail) {
                    Mail::to($partnerEmail)->send(new VisitOrderEmailPartner($subject, $partnerName, $visit_order_number, $debtor_name, $location, $due_date));
                }
            }
        }

        // Simpan data visit ke history
        if ($request->filled('partner_id')) {
            // If partner is filled, add an additional history entry for status change to 'Assigned'
            $visitOrderId = $visitOrder->visit_order_id;
            $statusHistoryDescOpen = 'Visit Order ' . $request->visit_order_number . ' status is Open!';
            $statusHistoryDescAssigned = 'Visit Order ' . $request->visit_order_number . ' status changed to Assigned!';
            $createdBy = session('user_id');

            // Save the history for status 'Open'
            $this->visit_order_history($visitOrderId, $statusHistoryDescOpen, $createdBy, 1); // Status code 1 for 'Open'

            // Save the history for status 'Assigned'
            $this->visit_order_history($visitOrderId, $statusHistoryDescAssigned, $createdBy, 2); // Status code 2 for 'Assigned'
        } else {
            // If partner is not filled, save only one history entry for status 'Open'
            $visitOrderId = $visitOrder->visit_order_id;
            $statusHistoryDescOpen = 'Visit Order ' . $request->visit_order_number . ' status is Open!';
            $createdBy = session('user_id');

            // Save the history for status 'Open'
            $this->visit_order_history($visitOrderId, $statusHistoryDescOpen, $createdBy, 1); // Status code 1 for 'Open'
        }

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Visit Order ' . $request->visit_order_number . ' created successfully!']]);
    }

    // simpan data ke debtor lewat autocomplete
    public function insert_data_to_debtor(Request $request)
    {
        // Cek apakah debtor_name sudah ada di tabel sys_debtor
        $debtor = Debtor::where('debtor_name', $request->debtor_name)->first();

        // Jika belum ada, simpan data baru ke tabel sys_debtor
        if (!$debtor) {
            $debtor = new Debtor();
            $debtor->debtor_status       = 2; // activated
            $debtor->debtor_name         = $request->debtor_name;
            $debtor->debtor_desc         = 'Generated from visit order - create.';
            $debtor->debtor_created_by   = session('user_id');
            $debtor->debtor_created_date = Carbon::now()->format('Y-m-d H:i:s');
            // Add other debtor data as needed
            $debtor->save();
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'Debtor Already exist!', 'text' => 'Debtor ' . $request->debtor_name . ' already exist!']]);
        }

        // Return the response with the debtor's ID
        return response()->json(['status' => true, 'debtor_id' => $debtor->debtor_id, 'message' => ['title' => 'Successfully saved!', 'text' => 'Debtor ' . $request->debtor_name . ' saved successfully!']]);
    }

    // simpan data ke site contact lewat autocomplete
    public function insert_data_to_site_contact(Request $request)
    {
        $site_contact = SiteContact::where('site_contact_fullname', $request->site_contact_fullname)->where('site_id', $request->site_id)->first();

        if (!$site_contact) {
            $site_contact = new SiteContact();
            $site_contact->site_contact_status       = 2; // activated
            $site_contact->site_id                   = $request->site_id;
            $site_contact->site_contact_fullname     = $request->site_contact_fullname;
            $site_contact->site_contact_created_by   = session('user_id');
            $site_contact->site_contact_created_date = Carbon::now()->format('Y-m-d H:i:s');
            $site_contact->save();
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'Site Contact Already exist!', 'text' => 'Site Contact ' . $request->site_contact_fullname . ' already exist!']]);
        }

        return response()->json(['status' => true, 'site_contact_id' => $site_contact->site_contact_id, 'message' => ['title' => 'Successfully saved!', 'text' => 'Site Contact ' . $request->site_contact_fullname . ' saved successfully!']]);
    }

    /**
     * Display data to provinces, regencies, districts and village
     */
    public function getProvinces()
    {
        $provinces = MasterProvinces::all();

        if ($provinces->count() > 0) {
            return response()->json(['status' => true, 'data' => $provinces]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }
    public function getRegencies(Request $request)
    {
        $provinceId = $request->input('provinceId');

        $regencies = MasterRegencies::where('province_id', $provinceId)->get();

        if ($regencies->count() > 0) {
            return response()->json(['status' => true, 'data' => $regencies]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

    public function getDistricts(Request $request)
    {
        $regencyId = $request->input('regencyId');

        $districts = MasterDistricts::where('regency_id', $regencyId)->get();

        if ($districts->count() > 0) {
            return response()->json(['status' => true, 'data' => $districts]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

    public function getVillages(Request $request)
    {
        $districtId = $request->input('districtId');

        $villages = MasterVillages::where('district_id', $districtId)->get();

        if ($villages->count() > 0) {
            return response()->json(['status' => true, 'data' => $villages]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

    /**
     * End Display data to provinces, regencies, districts and village
     */
}
