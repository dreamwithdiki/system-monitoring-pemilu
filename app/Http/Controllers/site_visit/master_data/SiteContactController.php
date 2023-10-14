<?php

namespace App\Http\Controllers\site_visit\master_data;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SiteContact;

class SiteContactController extends Controller
{
    public function find(Request $request, $id)
    {
      $search = $request->search;
      $sites = SiteContact::orderby('site_contact_fullname','asc')
        ->select('site_contact_id','site_contact_fullname')
        ->where('site_contact_fullname', 'like', '%' . $search . '%')
        ->where('site_id', $id)
        ->isActive()
        ->get();

      $response = array();
      foreach($sites as $site){
         $response[] = array(
              "id"    => $site->site_contact_id,
              "text"  => $site->site_contact_fullname
         );
      }

      return response()->json($response);
    }

    public function findAllSiteContact(Request $request, $id)
    {
        $search = $request->search;
        $siteContacts = SiteContact::orderby('site_contact_fullname', 'asc')
            ->select('site_contact_id', 'site_contact_fullname')
            ->where('site_contact_fullname', 'like', '%' . $search . '%')
            ->where('site_id', $id)
            ->isActive()
            ->get();

        $response = array();
        foreach ($siteContacts as $sc) {
            $response[] = array(
                "id" => $sc->site_contact_id,
                "value" => $sc->site_contact_fullname,
            );
        }

        return response()->json($response);
    }
}
