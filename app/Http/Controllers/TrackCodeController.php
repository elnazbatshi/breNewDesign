<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;

class TrackCodeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function terms()
    {
        return view('terms');
    }


    public function getTerms(Request $request)
    {
        $query = Term::query();
        if ($request->filter != 'All') {
            $query->Where('name', 'like', '%' . $request->filter . '%')
                ->orWhere('term_id', 'like', '%' . $request->filter . '%');
        }
        $terms = $query->get();
        return response(['response' => true, 'terms' => $terms]);
    }

    public function deleteTerm($id)
    {
        $term = Term::where('term_id',$id)->delete();
        return response(['response' => true, 'terms' => $term]);
    }
}