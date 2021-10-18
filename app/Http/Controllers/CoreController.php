<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoreController extends Controller
{
    private function responseBre($status, $status_message, $data)
    {

        header("HTTP/1.1 " . $status);

        $response['status'] = $status;
        $response['message'] = $status_message;
        $response['data'] = $data;

        echo json_encode($response);
        die();
    }

    private function array_sort($array, $on, $order = SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    public function proccessor(Request $request)
    {
        $token = 'sVR7nTA3O8S01XciOrjB5Y3vplzxLtBok';
        $action = $request->input('action');
        $trackcodes = $request->input('track_code');
        if ($request->input('token') != $token){
            $this->response(400, "Invalid Token", NULL);
            return;
        }
        switch ($action) {
            case 'get_details':
                $this->get_details($trackcodes);
                break;
            case 'get_details_min':
                $this->get_details_min($trackcodes);
                break;
            case 'get_details_min_auto_clean':
                $this->get_details_min_auto_clean($trackcodes);
                break;
            case 'add_details':
                $this->add_details();
                break;
            case 'Duplicate_Clean':
                $this->Duplicate_Clean();
                break;
            case 'edit_details':
                $this->edit_details();
                break;
            case 'LostTrackNumbers':
                $this->LostTrackNumbers();
                break;
            case 'DuplicateCleanBatch':
                $this->DuplicateCleanBatch();
                break;
            case 'LimitedSelect':
                $this->LimitedSelect();
                break;
            case 'delete_tracks':
                $this->delete_tracks();
                break;
        }
    }

    public function get_details($track_code)
    {
        if (empty($track_code)) $this->responseBre(400, "Tracking code should not be empty", NULL);
        if (!is_array($track_code)){
            $track_code = [$track_code];
        }
        $track_codes = implode(',', $track_code);
        $posts = DB::select("SELECT DISTINCT p1.meta_value as track_code, p2.meta_value as details FROM wdp_postmeta p1 JOIN wdp_postmeta p2 ON p2.post_id=p1.post_id WHERE p2.meta_key = 'wpt_location' AND p1.meta_key = 'wpt_tracking_code' AND p1.post_id != '0' AND p1.meta_value IN ($track_codes);");
        $terms = DB::select("SELECT term_id, name FROM wdp_terms;");
        $posts = array_values(array_map(function ($post) use ($terms) {
            $details = unserialize(unserialize($post->details));
            $details = array_values(array_map(function ($detail) use ($terms) {
                $term = array_values(array_filter($terms, function ($term) use ($detail) {
                    return ($term->term_id == $detail['status']);
                }));

                $detail['status'] = $term[0]->name;

                return $detail;
            }, $details));

            $details = array_values($this->array_sort($details, 'time', SORT_ASC));

            $post->details = $details;

            return $post;
        }, $posts));

        $this->responseBre(200, "Successful", $posts);
        return;
    }

    public function get_details_min($track_code)
    {
        if (empty($track_code)) $this->responseBre(400, "Tracking code should not be empty", NULL);
        if (!is_array($track_code)){
            $track_code = [$track_code];
        }
        $track_codes = implode(',', $track_code);
        $posts = DB::select("SELECT DISTINCT p1.meta_value as track_code, p2.meta_value as details FROM wdp_postmeta p1 JOIN wdp_postmeta p2 ON p2.post_id=p1.post_id WHERE p2.meta_key = 'wpt_location' AND p1.meta_key = 'wpt_tracking_code' AND p1.post_id != '0' AND p1.meta_value IN ($track_codes);");
        $terms = DB::select("SELECT term_id, name FROM wdp_terms;");
        $posts = array_values(array_map(function ($post) use ($terms) {
            $details = unserialize(unserialize($post->details));
            $details = array_values(array_map(function ($detail) use ($terms) {
                $term = array_values(array_filter($terms, function ($term) use ($detail) {
                    if(isset($term->term_id) && $term->term_id != '' && isset($detail['status']) && $detail['status'] != ''){
                        return ($term->term_id == $detail['status']);
                    }
                }));
                if(isset($term[0]->name) && $term[0]->name != ''){
                    $detail['status'] = $term[0]->name;
                }
                return $detail;
            }, $details));
            $details = array_values($this->array_sort($details, 'time', SORT_ASC));
            $post->details = $details;
            return $post;
        }, $posts));
        $FilterStatus = array();
        $Filtered = array();
        for ($i = 0; $i <= COUNT($posts[0]->details); $i++) {
            if (isset($posts[0]->details[$i]['status']) && $posts[0]->details[$i]['status'] != '' && !in_array($posts[0]->details[$i]['status'] . '*' . $posts[0]->details[$i]['time'], $FilterStatus)) {
                array_push($FilterStatus, $posts[0]->details[$i]['status'] . '*' . $posts[0]->details[$i]['time']);
                if($posts[0]->details[$i] != null){
                    array_push($Filtered, $posts[0]->details[$i]);
                }
            }
        }
        unset($posts[0]->details);
        $posts['details'] = $Filtered;
        $this->responseBre(200, "Successful", $posts);
    }

    public function get_details_min_auto_clean($track_code)
    {
        if (empty($track_code)) $this->responseBre(400, "Tracking code should not be empty", NULL);
        if (!is_array($track_code)){
            $track_code = [$track_code];
        }
        $track_codes = implode(',', $track_code);
        $postsWithTerm =DB::select("SELECT DISTINCT p1.meta_value as track_code, p2.meta_value as details FROM wdp_postmeta p1 JOIN wdp_postmeta p2 ON p2.post_id=p1.post_id WHERE p2.meta_key = 'wpt_location' AND p1.meta_key = 'wpt_tracking_code' AND p1.post_id != '0' AND p1.meta_value IN ($track_codes);");
        $posts =DB::select("SELECT DISTINCT p1.meta_value as track_code, p2.meta_value as details FROM wdp_postmeta p1 JOIN wdp_postmeta p2 ON p2.post_id=p1.post_id WHERE p2.meta_key = 'wpt_location' AND p1.meta_key = 'wpt_tracking_code' AND p1.post_id != '0' AND p1.meta_value IN ($track_codes);");
        $terms = DB::select("SELECT term_id, name FROM wdp_terms;");


        $posts = array_values(array_map(function ($post) use ($terms) {
            $details = unserialize(unserialize($post->details));
            $details = array_values(array_map(function ($detail) use ($terms) {
                $term = array_values(array_filter($terms, function ($term) use ($detail) {
                    if(isset($term->term_id) && $term->term_id != '' && isset($detail['status']) && $detail['status'] != ''){
                        return ($term->term_id == $detail['status']);
                    }
                }));
                if(isset($term[0]->name) && $term[0]->name != ''){
                    $detail['status'] = $term[0]->name;
                }
                return $detail;
            }, $details));
            $details = array_values($this->array_sort($details, 'time', SORT_ASC));
            $post->details = $details;
            return $post;
        }, $posts));
        $this->responseBre(200, "Successful", $posts[0]->post_id);
        $post_id = $posts[0]->post_id;
        $postsWithTerm = array_values(array_map(function ($postwt) use ($terms) {

            $details = unserialize(unserialize($postwt->details));
            $details = array_values(array_map(function ($detail) use ($terms) {
                $term = array_values(array_filter($terms, function ($term) use ($detail) {
                    if(isset($term->term_id) && $term->term_id != '' && isset($detail['status']) && $detail['status'] != ''){
                        return ($term->term_id == $detail['status']);
                    }
                }));
                if(isset($term[0]->name) && $term[0]->name != ''){
                    $detail['status'] = $term[0]->name;
                }
                return $detail;
            }, $details));
            $details = array_values($this->array_sort($details, 'time', SORT_ASC));
            $postwt->details = $details;
            return $postwt;
        }, $postsWithTerm));
        //
        $FilterStatus = array();
        $Filtered = array();
        $FilteredWithTerm = array();
        for ($i = 0; $i <= COUNT($posts[0]->details); $i++) {
            if (isset($posts[0]->details[$i]['status']) && $posts[0]->details[$i]['status'] != '' && !in_array($posts[0]->details[$i]['status'] . '*' . $posts[0]->details[$i]['time'], $FilterStatus)) {
                array_push($FilterStatus, $posts[0]->details[$i]['status'] . '*' . $posts[0]->details[$i]['time']);
                array_push($Filtered, $posts[0]->details[$i]);
                array_push($FilteredWithTerm, $postsWithTerm[0]->details[$i]);
            }
        }
        unset($postsWithTerm[0]->details);
        $postsWithTerm['details'] = $FilteredWithTerm;
        //-----------------------------------------
        $wpt_location = serialize(serialize($Filtered));
        DB::update("UPDATE wdp_postmeta SET meta_value = '$wpt_location' WHERE post_id = '$post_id' AND meta_key = 'wpt_location'");
        $this->responseBre(200, "Successful", $postsWithTerm);
    }

    public function add_details()
    {
        return 'add_details';
    }

    public function Duplicate_Clean()
    {
        return 'Duplicate_Clean';
    }

    public function add_group_details()
    {
        return 'add_group_details';
    }

    public function edit_details()
    {
        return 'edit_details';
    }

    public function LostTrackNumbers()
    {
        return 'LostTrackNumbers';
    }

    public function DuplicateCleanBatch()
    {
        return 'DuplicateCleanBatch';
    }

    public function LimitedSelect()
    {
        return 'LimitedSelect';
    }
    public function delete_tracks()
    {
        return 'delete_tracks';
    }
}
