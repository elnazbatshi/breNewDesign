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
        if ($request->input('token') != $token){
            $this->response(400, "Invalid Token", NULL);
            return;
        }
        switch ($action) {
            case 'get_details':
                $trackcodes = $request->input('track_code');
                $this->get_details($trackcodes);
                break;
            case 'get_details_min':
                $trackcodes = $request->input('track_code');
                $this->get_details_min($trackcodes);
                break;
            case 'get_details_min_auto_clean':
                $trackcodes = $request->input('track_code');
                $this->get_details_min_auto_clean($trackcodes);
                break;
            case 'add_details':
                $this->add_details($request);
                break;
            case 'Duplicate_Clean':
                $this->Duplicate_Clean($request);
                break;
            case 'edit_details':
                $this->edit_details($request);
                break;
            case 'LostTrackNumbers':
                $this->LostTrackNumbers($request);
                break;
            case 'DuplicateCleanBatch':
                $this->DuplicateCleanBatch($request);
                break;
            case 'LimitedSelect':
                $this->LimitedSelect($request);
                break;
            case 'delete_tracks':
                $this->delete_tracks($request);
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
                if(isset($term[0]->name) && $term[0]->name != ''){
                    $detail['status'] = $term[0]->name;
                }

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
        $postsWithTerm =DB::select("SELECT DISTINCT p1.meta_value as track_code,p1.post_id as post_id, p2.meta_value as details FROM wdp_postmeta p1 JOIN wdp_postmeta p2 ON p2.post_id=p1.post_id WHERE p2.meta_key = 'wpt_location' AND p1.meta_key = 'wpt_tracking_code' AND p1.post_id != '0' AND p1.meta_value IN ($track_codes);");
        $posts =DB::select("SELECT DISTINCT p1.meta_value as track_code,p1.post_id as post_id, p2.meta_value as details FROM wdp_postmeta p1 JOIN wdp_postmeta p2 ON p2.post_id=p1.post_id WHERE p2.meta_key = 'wpt_location' AND p1.meta_key = 'wpt_tracking_code' AND p1.post_id != '0' AND p1.meta_value IN ($track_codes);");
        $terms = DB::select("SELECT term_id, name FROM wdp_terms;");
        $post_id = $posts[0]->post_id;
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
                if($detail != null){
                    return $detail;
                }
            }, $details));
            $details = array_values($this->array_sort($details, 'time', SORT_ASC));
            $post->details = $details;
            return $post;
        }, $posts));

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

    public function add_details($request)
    {
        $username = $request->username;
        $track_code = $request->track_code;
        $date = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $request->date);
        $location = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $request->track_code);
        $status = $request->status;
        $receiver = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $request->receiver);
        $user =DB::select("SELECT ID FROM wdp_users WHERE user_login = '$username' LIMIT 1");
        $user_id = $user[0]->ID;
        if (!isset($user_id))
            $this->responseBre(400, "Username doesn't exists", NULL);

        $term =DB::select("SELECT term_id FROM wdp_terms WHERE name = \"$status\" LIMIT 1");
        $status_id = $term[0]->term_id;
        if (!isset($status_id))
            $this->responseBre(400, "Status id doesn't exists", NULL);

        $post = DB::select("SELECT post_id FROM wdp_postmeta WHERE meta_key = 'wpt_tracking_code' AND meta_value = \"$track_code\" AND post_id !=0 LIMIT 1;");
        if (isset( $post[0]->post_id) &&  $post[0]->post_id != 0 &&  $post[0]->post_id != '') {
            $post_id = $post[0]->post_id;
            $postTest = $post_id;
        }
        $newdate = explode(' ', $date);
        $tarikh = explode('/', $newdate[0]);
        $zaman = explode(':', $newdate[1]);
        unset($zaman[2]);
        $tarikh[1] = (strlen($tarikh[1]) < 2) ? '0' . $tarikh[1] : $tarikh[1];
        $tarikh[2] = (strlen($tarikh[2]) < 2) ? '0' . $tarikh[2] : $tarikh[2];
        $zaman[0] = (strlen($zaman[0]) < 2) ? '0' . $zaman[0] : $zaman[0];
        $zaman[1] = (strlen($zaman[1]) < 2) ? '0' . $zaman[1] : $zaman[1];
        $date = implode('-', $tarikh) . ' ' . implode(':', $zaman);
        $date = str_replace('-0-0 ', ' ', $date);
        if (isset($post_id) && $post_id != 0 && $post_id != '') {
            //update wpt_location
            $post = DB::select("SELECT meta_value FROM wdp_postmeta WHERE post_id = '$post_id' AND meta_key = 'wpt_location' LIMIT 1;");

            $meta_value_cache = unserialize(unserialize($post[0]->meta_value));
            $NewValues = array();
            if (is_array($meta_value_cache)) {
                $wpt_location = $meta_value_cache;
            } else {
                $wpt_location = array();
            }
            $NewValues = array('time' => $date, 'location' => $location, 'status' => $status_id, 'count' => '', 'receiver' => $receiver);
            array_push($wpt_location, $NewValues);
            $wpt_location = serialize(serialize($wpt_location));
            $post = DB::update("UPDATE wdp_postmeta SET meta_value = '$wpt_location' WHERE post_id = '$post_id' AND meta_key = 'wpt_location';");
            if (COUNT($post) > 0) {
                $this->responseBre(200, "Successful", ['track_code' => $track_code]);
            }
        } else {
            //insert new post
            $date_Now = date("Y-m-d H:i:s");
            $post_id = DB::table('wdp_posts')->insertGetId(
                ['post_date' => $date_Now,
                'post_date_gmt' => $date_Now,
                'post_modified' => $date_Now,
                'post_modified_gmt' => $date_Now,
                'post_excerpt' => '',
                'to_ping' => '',
                'pinged' => '',
                'post_content_filtered' => '',
                'post_title' => '',
                'post_content' => '',
                'post_author' => $user_id,
                'post_status' =>'publish',
                'post_type' => 'transport']
            );
            $wpt_location = array('3' => array('time' => $date, 'location' => $location, 'status' => $status_id, 'count' => '', 'receiver' => $receiver));
            $wpt_location = serialize(serialize($wpt_location));
            $post_id = DB::table('wdp_postmeta')->insertGetId(
                ['post_id' => $post_id,
                'meta_key' => 'wpt_tracking_code',
                'meta_value' => $track_code],
            );
            $post_id = DB::table('wdp_postmeta')->insertGetId(
                ['post_id' => $post_id,
                'meta_key' => 'wpt_location',
                'meta_value' => $wpt_location],
            );
            $this->responseBre(200, "Successful", ['track_code2' => $track_code]);
        }
    }

    public function Duplicate_Clean($request)
    {
        $track_code=$request->track_code;
        if (!is_array($track_code))
            $track_code = [$track_code];

        $track_codes = implode(',', $track_code);
        $posts=DB::select("SELECT DISTINCT p1.meta_value as track_code,p1.post_id as post_id, p2.meta_value as details FROM wdp_postmeta p1 JOIN wdp_postmeta p2 ON p2.post_id=p1.post_id WHERE p2.meta_key = 'wpt_location' AND p1.meta_key = 'wpt_tracking_code' AND p1.post_id != '0' AND p1.meta_value IN ($track_codes);");
        $post_id = $posts[0]->post_id;
        $terms = DB::select("SELECT term_id, name FROM wdp_terms;");
        $posts = array_values(array_map(function ($post) use ($terms) {
            $details = unserialize(unserialize($post->details));
            $details = array_values(array_map(function ($detail) use ($terms) {
                $term = array_values(array_filter($terms, function ($term) use ($detail) {
                    if(isset($term->term_id) && $term->term_id != '' && isset($detail['status']) && $detail['status'] != ''){
                        return ($term->term_id == $detail['status']);
                    }
                }));
                //$detail['status'] = $term[0]['name'];
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
                array_push($Filtered, $posts[0]->details[$i]);
            }
        }
        //-----------------------------------------
        $wpt_location = serialize(serialize($Filtered));
        $post = DB::update("UPDATE wdp_postmeta SET meta_value = '$wpt_location' WHERE post_id = '$post_id' AND meta_key = 'wpt_location'");
        $this->responseBre(200, "Successful", ['track_code' => 'Successfully']);
    }

    public function add_group_details($request)
    {
        $username = $request->username;
        $data = $request->data;

        $user = DB::select("SELECT ID FROM wdp_users WHERE user_login = '$username' LIMIT 1");
        $user_id = $user['ID'];

        if (!isset($user_id))
            $this->responseBre(400, "Username doesn't exists", NULL);

        if (!is_array($data))
            $this->responseBre(400, "Data must be an array of objects", NULL);

        $failure = [];
        $success = [];

        foreach ($data as $dt) {
            $track_code = $dt['track_code'];
            $date = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $dt['date']);
            $location = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $dt['location']);
            $status = $dt['status'];
            $receiver = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $dt['receiver']);

            $term = DB::select("SELECT term_id FROM " . prefix . "terms WHERE name = \"$status\" LIMIT 1");

            $status_id = $term['term_id'];

            if (!isset($status_id)) {
                $failure[] = $track_code;
                continue;
            }

            $post = DB::select("SELECT post_id FROM " . prefix . "postmeta WHERE meta_key = 'wpt_tracking_code' AND meta_value = '$track_code' AND LIMIT 1");
            $post_id = $post['post_id'];

            $newdate = explode(' ', $date);
            $tarikh = explode('/', $newdate[0]);
            $zaman = explode(':', $newdate[1]);
            unset($zaman[2]);

            $tarikh[1] = (strlen($tarikh[1]) < 2) ? '0' . $tarikh[1] : $tarikh[1];
            $tarikh[2] = (strlen($tarikh[2]) < 2) ? '0' . $tarikh[2] : $tarikh[2];

            $zaman[0] = (strlen($zaman[0]) < 2) ? '0' . $zaman[0] : $zaman[0];
            $zaman[1] = (strlen($zaman[1]) < 2) ? '0' . $zaman[1] : $zaman[1];

            $date = implode('-', $tarikh) . ' ' . implode(':', $zaman);
            $date = str_replace('-0-0 ', ' ', $date);

            if (isset($post_id) && $post_id != '0' && $post_id != '') {
                //update wpt_location

                $post = DB::select("SELECT meta_value FROM wdp_postmeta WHERE post_id = '$post_id' AND meta_key = 'wpt_location' LIMIT 1");
                $wpt_location = unserialize(unserialize($post['meta_value']));

                array_push($wpt_location, array('time' => $date, 'location' => $location, 'status' => $status_id, 'count' => '', 'receiver' => $receiver));

                $wpt_location = serialize(serialize($wpt_location));

                $post = DB::update("UPDATE wdp_postmeta SET meta_value = '$wpt_location' WHERE post_id = '$post_id' AND meta_key = 'wpt_location'");
                if (mysqli_affected_rows($connect) > 0)
                    $success[] = $track_code;
            } else {
                //insert new post
                $date_Now = date("Y-m-d H:i:s");
                $post_id = DB::table('wdp_posts')->insertGetId(
                    ['post_date' => $date_Now,
                        'post_date_gmt' => $date_Now,
                        'post_modified' => $date_Now,
                        'post_modified_gmt' => $date_Now,
                        'post_excerpt' => '',
                        'to_ping' => '',
                        'pinged' => '',
                        'post_content_filtered' => '',
                        'post_title' => '',
                        'post_content' => '',
                        'post_author' => $user_id,
                        'post_status' =>'publish',
                        'post_type' => 'transport']
                );
                $wpt_location = array('3' => array('time' => $date, 'location' => $location, 'status' => $status_id, 'count' => '', 'receiver' => $receiver));
                $wpt_location = serialize(serialize($wpt_location));
                $post = DB::table('wdp_postmeta')->insertGetId(
                    ['post_id' => $post_id,
                        'meta_key' => 'wpt_tracking_code',
                        'meta_value' => $track_code],
                );
                $post = DB::table('wdp_postmeta')->insertGetId(
                    ['post_id' => $post_id,
                        'meta_key' => 'wpt_location',
                        'meta_value' => $wpt_location],
                );
                $success[] = $track_code;
            }
        }

        $this->responseBre(200, "Successful", ['success' => $success, 'failure' => $failure]);
    }

    public function edit_details($request)
    {
        $username = $request->username;
        $track_code = $request->track_code;
        $data = $request->data;
        $user = DB::select("SELECT ID FROM wdp_users WHERE user_login = '$username' LIMIT 1");
        $user_id = $user[0]->ID;
        if (!isset($user_id))
            $this->responseBre(400, "Username doesn't exists", NULL);

        if (!is_array($data))
            $this->responseBre(400, "Data must be an array of objects", NULL);

        $post = DB::select("SELECT post_id FROM wdp_postmeta WHERE meta_key = 'wpt_tracking_code' AND meta_value = '$track_code' LIMIT 1");
        $post_id = $post[0]->post_id;

        if (!isset($post_id)) {
            $this->responseBre(400, "There is no post with track number: {$track_code}", NULL);
        }

        $wpt_location = [];

        foreach ($data as $dt) {
            $date = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $dt[0]['date']);
            $location = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $dt[0]['location']);
            $status = $dt[0]['status'];
            $receiver = preg_replace('/[^a-z0-9_\-\/\'\": ]/i', '', $dt[0]['receiver']);

            $term = DB::select("SELECT term_id FROM wdp_terms WHERE name = \"$status\" LIMIT 1");
            $status_id = $term[0]->term_id;

            if (!isset($status_id) || $status == '')
                $this->responseBre(400, "The status '{$status}' is not exists Status Id :" . $status_id, NULL);


            $newdate = explode(' ', $date);
            $tarikh = explode('/', $newdate[0]);
            $zaman = explode(':', $newdate[1]);
            unset($zaman[2]);

            $tarikh[1] = (strlen($tarikh[1]) < 2) ? '0' . $tarikh[1] : $tarikh[1];
            $tarikh[2] = (strlen($tarikh[2]) < 2) ? '0' . $tarikh[2] : $tarikh[2];

            $zaman[0] = (strlen($zaman[0]) < 2) ? '0' . $zaman[0] : $zaman[0];
            $zaman[1] = (strlen($zaman[1]) < 2) ? '0' . $zaman[1] : $zaman[1];

            $date = implode('-', $tarikh) . ' ' . implode(':', $zaman);
            $date = str_replace('-0-0 ', ' ', $date);

            $wpt_location[] = array('time' => $date, 'location' => $location, 'status' => $status_id, 'count' => '', 'receiver' => $receiver);
        }

        $wpt_location = serialize(serialize($wpt_location));

        DB::update("UPDATE wdp_postmeta SET meta_value = '$wpt_location' WHERE post_id = '$post_id' AND meta_key = 'wpt_location'");
        $this->responseBre(200, "Successful", ['track_code' => $track_code]);

    }

    public function LostTrackNumbers($request)
    {
        $posts = DB::select("SELECT * FROM wdp_postmeta WHERE post_id = 0 AND meta_key = 'wpt_tracking_code' ORDER BY meta_id DESC ");
        $this->responseBre(200, "Successful", $posts);
    }

    public function DuplicateCleanBatch($request)
    {
        return 'DuplicateCleanBatch';
    }

    public function LimitedSelect($request)
    {
        $range = explode('-',$request->range);
        $from = (int)$range[0];
        $to = (int)$range[1];
        $posts = DB::select("SELECT * FROM wdp_postmeta WHERE post_id != 0 AND meta_key = 'wpt_tracking_code' ORDER BY meta_id DESC  LIMIT $from,$to");
        $this->responseBre(200, "Successful", $posts);
    }
    public function delete_tracks($request)
    {
        if (empty($_REQUEST['track_code'])) $this->responseBre(400, "Tracking code should not be empty", NULL);
        $track_code = $request->track_code;
        if (!is_array($track_code))
            $track_code = [$track_code];

        $success = [];
        foreach ($track_code as $tc) {
            $post = DB::select("SELECT post_id FROM wdp_postmeta WHERE meta_key = 'wpt_tracking_code' AND meta_value = '$tc' LIMIT 1");
            $post_id = $post[0]->post_id;
            if (isset($post_id)) {
                DB::delete("DELETE FROM wdp_posts WHERE id = {$post_id}");
                DB::delete("DELETE FROM wdp_postmeta WHERE post_id = {$post_id}");
            }
            $success[] = $tc;
        }
        $this->responseBre(200, "Successful", ['track_code' => $success]);
    }
}
