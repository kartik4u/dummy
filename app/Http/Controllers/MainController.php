<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostContention;
use App\Models\PostGraphResult;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SubCategory;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\User;
use Auth;
use DB;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mail;
use Redirect;
use Response;
use View;

class MainController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')
            ->with(['post_media' => function ($q) {
                $q->select('*', 'media_name as media_name_medium');
            }])
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->where('status', 1)
            ->limit(6)
        //->get(['category_id', DB::raw('MAX(id) as id,title,slug,created_at,updated_at')]);
            ->get();

        if (isset(Auth::user()->role_id) == 2) {
            $subscription = Subscription::where('user_id', Auth::user()->id)->where('plan_end_date', '>', time())->orderBy('created_at', 'desc')->first();
            if ($subscription) {
                $subscription_status = 1;
            } else {
                $subscription_status = 2;
            }
        } else {
            $subscription_status = 3;
        }

        return view('main', array('posts' => $posts, 'subscription_status' => $subscription_status));
    }

    /*
     * Main Function for subscribe user
     * @param Request $request (email)
     * @return type (status, success/error)
     */

    public function subscribed(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $user = UserSubscription::where('email', $request->email)->where('status', 1)->first();

        if ($user) {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'You have already subscribed the newsletter.');

            return Redirect::back();
        } else {
            $subs = UserSubscription::updateOrCreate(
                ['email' => $request->email], ['created_at' => time(), 'updated_at' => time(), 'status' => 1]
            );

            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'You have successfully subscribed the newsletter.');
            return Redirect::back();
        }
    }

    public function getGraph(Request $request)
    {

        $request_data = $request->all();
        if ($request_data['id']) {
            $latest_post_id = $request_data['id'];
        } else {
            $latest_post_data = Post::select('*')->whereHas('post_result', function ($q) {
                $q->where('status', 1);
            })
                ->withCount('post_result')
                ->orderBy('post_result_count', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
            $latest_post_id = $latest_post_data['id'];
        }
        $first_category = PostGraphResult::where('post_id', $latest_post_id)->where('question_category_id', 1)
            ->select('*', DB::raw('count(*) as total'))
            ->groupBy('option_id')
            ->orderBy('option_id', 'desc')
            ->get()->toArray();
        $second_category = PostGraphResult::where('post_id', $latest_post_id)->where('question_category_id', 2)
            ->select('*', DB::raw('count(*) as total'))
            ->groupBy('option_id')
            ->orderBy('option_id', 'desc')
            ->get()->toArray();
        $third_category = PostGraphResult::where('post_id', $latest_post_id)->where('question_category_id', 3)
            ->select('*', DB::raw('count(*) as total'))
            ->groupBy('option_id')
            ->orderBy('option_id', 'desc')
            ->get()->toArray();
//        print_r($second_category);exit;
        $first = [];
        $second = [];
        $third = [];
        $first_array = [];
        $second_array = [];
        $third_array = [];
        $final_array = [];
        foreach ($first_category as $key => $first_category_data) {
            $first[$first_category_data['option_id'] - 1] = (int) $first_category_data['total'];
        }
        foreach ($second_category as $key1 => $second_category_data) {
            $second[$second_category_data['option_id'] - 1] = (int) $second_category_data['total'];
        }
        foreach ($third_category as $key2 => $third_category_data) {
            $third[$third_category_data['option_id'] - 1] = (int) $third_category_data['total'];
        }

        $first_array = array(isset($first[0]) ? $first[0] : '', isset($second[0]) ? $second[0] : '', isset($third[0]) ? $third[0] : '');
        $second_array = array(isset($first[1]) ? $first[1] : '', isset($second[1]) ? $second[1] : '', isset($third[1]) ? $third[1] : '');
        $third_array = array(isset($first[2]) ? $first[2] : '', isset($second[2]) ? $second[2] : '', isset($third[2]) ? $third[2] : '');

        $final_array[] = ['name' => "Green(Good news)", 'data' => $first_array];
        $final_array[] = ['name' => "Orange(Some misguidance)", 'data' => $second_array];
        $final_array[] = ['name' => "Red(Bad news)", 'data' => $third_array];
        if ($final_array) {
            $data['status'] = 200;
            $data['message'] = 'Data has followed';
            $data['data'] = $final_array;
            $data['post_name'] = @$latest_post_data['title'];
            return Response::json($data);
        } else {
            $data['status'] = 400;
            $data['message'] = 'Data has not found';
            return Response::json($data);
        }
    }

    /*
     * Main Function to verify user
     * @param Request $request (verification_code)
     * @return type (verify blade)
     */

    public function userActivation($key, Request $request)
    {

        $user = User::where('verification_code', $key)->first();
        if ($user) {
            if ($user->secondary_email) {
                $user->update([
                    'email' => $user->secondary_email,
                    'verification_code' => '',
                    'secondary_email' => '',
                ]);
                Auth::logout();
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Your email has been changed successfully.Please Login with your new email');
                return Redirect::route('login');
            } else {
                $user->update([
                    'status' => '1',
                    'verification_code' => '',
                ]);

                $user_activity_login = ActivityLog::updateOrCreate(
                    ['user_id' => $user->id, 'meta_key' => 'account_verification'], ['user_id' => $user->id, 'meta_key' => 'account_verification', 'meta_value' => time(), 'status' => 1, 'created_at' => time(), 'updated_at' => time()]
                );

                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Your account has been verified.Please login your account.');

                return Redirect::route('login');
            }
        } else {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Your verify token expired.Please make another request for verify account.');

            return Redirect::route('login');
        }
    }

    /**
     * Get all post with category
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllPostCategory($category_slug, Request $request)
    {
        //$user_id = Auth::user()->id;
        $page_record = \Config::get('variable.page_per_record');

        $category = Category::where('slug', $category_slug)->select('id', 'title', 'slug')->first();

        $posts = Post::where('status', 1)
            ->orderBy('updated_at', 'desc')
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->whereHas('category', function ($q) use ($category_slug) {
                $q->where('slug', $category_slug);
            });

        $posts = $posts->paginate($page_record);
        return view('posts.all-post', ['category' => $category, 'posts' => $posts->appends(Input::except('page'))]);
    }

    /**
     * Get all post with category
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllPostSubCategory($category_slug, $subcategory_slug, Request $request)
    {
        //$user_id = Auth::user()->id;
        $page_record = \Config::get('variable.page_per_record');

        $category = Category::where('slug', $category_slug)->select('id', 'title', 'slug')->first();
        $subcategory = SubCategory::where('slug', $subcategory_slug)->select('id', 'title', 'slug')->first();

        $posts = Post::where('status', 1)
            ->orderBy('updated_at', 'desc')
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->whereHas('subcategory', function ($q) use ($subcategory_slug) {
                $q->where('slug', $subcategory_slug);
            });

        $posts = $posts->paginate($page_record);
        return view('posts.all-post', ['category' => $category, 'subcategory' => $subcategory, 'posts' => $posts->appends(Input::except('page'))]);
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('term', '');
        $posts = Post::where('title', 'LIKE', '%' . $query . '%')
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('created_at', 'desc')
            ->where('status', 1)
        //->orWhere('claims', 'LIKE', '%' . $query . '%')
        // ->orWhere('contentions', 'LIKE', '%' . $query . '%')
        //->orWhere('source_media', 'LIKE', '%' . $query . '%')
            ->get();
        $data = array();
        foreach ($posts as $post) {
            $data[] = array('value' => $post->title, 'id' => $post->id);
        }

        return $data; // return ['value' => 'No Result Found', 'id' => ''];
    }

    /**
     * Search news post
     *
     * @return \Illuminate\Http\Response
     */
    public function searchPost(Request $request)
    {
        $query1 = $request->get('term', '');
        $page_record = \Config::get('variable.page_per_record');
        $posts = Post::where(function ($query) use ($query1) {
            $query->where('title', 'LIKE', '%' . $query1 . '%')
                ->orWhere('claims', 'LIKE', '%' . $query1 . '%')
                ->orWhere('contentions', 'LIKE', '%' . $query1 . '%');
        })
            ->orderBy('updated_at', 'desc')
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->with(['post_media' => function ($q) {
                $q->select('*', 'media_name as media_name_medium');
            }])
            ->where('status', 1);

        $posts = $posts->paginate($page_record);
        return view('posts.search-post', ['posts' => $posts->appends(Input::except('page'))]);
    }

    /**
     * Get single post detail
     *
     * @return \Illuminate\Http\Response
     */
    public function getSinglePost($id)
    {
        $post = Post::where('slug', $id)
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->with(['all_post_media' => function ($q) {
                $q->select('*', 'media_name as media_name_thumb');
            }])
            ->first();

        $embended_url = $this->parseVideos($post->video_url);
        $video_url = @$embended_url[0]['url'];
        $page_record = \Config::get('variable.page_per_record');
        $get_contention = PostContention::where('post_id', $post->id)->orderBy('created_at', 'desc')->paginate($page_record);
        if (Auth::user()) {
            $get_user_contention = PostGraphResult::where('post_id', $post->id)->where('user_id', Auth::user()->id)->first();
        }
        $previous = Post::where('id', '<', $post->id)
            ->where('status', 1)
            ->max('id', 'title');
        $prev = Post::where('id', $previous)
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->first();

        $next = Post::where('id', '>', $post->id)
            ->where('status', 1)
            ->min('id');

        $nex = Post::where('id', $next)
            ->whereHas('category', function ($q) {
                $q->where('status', 1);
            })
            ->first();

        $post_red_alert = PostGraphResult::where('option_id', 3)->where('post_id', $post->id)->count();
        $post_yellow_alert = PostGraphResult::where('option_id', 2)->where('post_id', $post->id)->count();
        $post_green_alert = PostGraphResult::where('option_id', 1)->where('post_id', $post->id)->count();
        if ($post_red_alert != 0) {
            $alert_variable = 3;
        } elseif ($post_red_alert == 0 && $post_yellow_alert != 0) {
            $alert_variable = 2;
        } elseif ($post_red_alert == 0 && $post_yellow_alert == 0 && $post_green_alert != 0) {
            $alert_variable = 1;
        } else {
            $alert_variable = 4;
        }
        $question = Question::where('status', 1)->get();
        $question_option = QuestionOption::where('status', 1)->get();

        if (isset(Auth::user()->role_id) == 2) {
            $subscription = Subscription::where('user_id', Auth::user()->id)->where('plan_end_date', '>', time())->orderBy('created_at', 'desc')->first();
            if ($subscription) {
                $subscription_status = 1;
            } else {
                $subscription_status = 2;
            }
        } else {
            $subscription_status = 3;
        }

        if (Auth::user()) {
            return view('posts/post-view', ['posts' => $post, 'url' => url('/'), 'video_url' => $video_url, 'subscription_status' => $subscription_status, 'question' => $question, 'contention' => $get_contention->appends(Input::except('page')), 'get_user_contention' => $get_user_contention, 'question_option' => $question_option, 'previous' => $prev, 'next' => $nex, 'alert_variable' => $alert_variable]);
        } else {
            return view('posts/post-view', ['posts' => $post, 'url' => url('/'), 'video_url' => $video_url, 'subscription_status' => $subscription_status, 'question' => $question, 'contention' => $get_contention->appends(Input::except('page')), 'question_option' => $question_option, 'previous' => $prev, 'next' => $nex, 'alert_variable' => $alert_variable]);
        }
    }

    public function getSinglePostGraph($id = null)
    {

        $request_data = $request->all();
        $first_category = PostGraphResult::where('post_id', $id)->where('question_category_id', 1)
            ->select('*', DB::raw('count(*) as total'))
            ->groupBy('option_id')
            ->orderBy('option_id', 'desc')
            ->get()->toArray();
        $second_category = PostGraphResult::where('post_id', $id)->where('question_category_id', 2)
            ->select('*', DB::raw('count(*) as total'))
            ->groupBy('option_id')
            ->orderBy('option_id', 'desc')
            ->get()->toArray();
        $third_category = PostGraphResult::where('post_id', $id)->where('question_category_id', 3)
            ->select('*', DB::raw('count(*) as total'))
            ->groupBy('option_id')
            ->orderBy('option_id', 'desc')
            ->get()->toArray();
        $first = [];
        $second = [];
        $third = [];
        $first_array = [];
        $second_array = [];
        $third_array = [];
        $final_array = [];
        foreach ($first_category as $key => $first_category_data) {
            $first[$key] = $first_category_data['total'];
        }
        foreach ($second_category as $key1 => $second_category_data) {
            $second[$key1] = $second_category_data['total'];
        }
        foreach ($third_category as $key2 => $third_category_data) {
            $third[$key2] = $third_category_data['total'];
        }

        $first_array = array(isset($first[0]) ? $first[0] : 0, isset($second[0]) ? $second[0] : 0, isset($third[0]) ? $third[0] : 0);
        $second_array = array(isset($first[1]) ? $first[1] : 0, isset($second[1]) ? $second[1] : 0, isset($third[1]) ? $third[1] : 0);
        $third_array = array(isset($first[2]) ? $first[2] : 0, isset($second[2]) ? $second[2] : 0, isset($third[2]) ? $third[2] : 0);

        $final_array[] = ['name' => "red", 'data' => $first_array];
        $final_array[] = ['name' => "yellow", 'data' => $second_array];
        $final_array[] = ['name' => "green", 'data' => $third_array];
        if ($final_array) {
            $data['status'] = 200;
            $data['message'] = 'Data has followed';
            $data['data'] = $final_array;
            return Response::json($data);
        } else {
            $data['status'] = 400;
            $data['message'] = 'Data has not found';
            return Response::json($data);
        }
    }

    public function parseVideos($videoString = null)
    {
        // return data
        $videos = array();
        if (!empty($videoString)) {
            // split on line breaks
            $videoString = stripslashes(trim($videoString));
            $videoString = explode("\n", $videoString);
            $videoString = array_filter($videoString, 'trim');
            // check each video for proper formatting
            foreach ($videoString as $video) {
                // check for iframe to get the video url
                if (strpos($video, 'iframe') !== false) {
                    // retrieve the video url
                    $anchorRegex = '/src="(.*)?"/isU';
                    $results = array();
                    if (preg_match($anchorRegex, $video, $results)) {
                        $link = trim($results[1]);
                    }
                } else {
                    // we already have a url
                    $link = $video;
                }
                // if we have a URL, parse it down
                if (!empty($link)) {
                    // initial values
                    $video_id = null;
                    $videoIdRegex = null;
                    $results = array();
                    // check for type of youtube link
                    if (strpos($link, 'youtu') !== false) {
                        if (strpos($link, 'youtube.com') !== false) {
                            // works on:
                            // http://www.youtube.com/embed/VIDEOID
                            // http://www.youtube.com/embed/VIDEOID?modestbranding=1&amp;rel=0
                            // http://www.youtube.com/v/VIDEO-ID?fs=1&amp;hl=en_US
                            //$videoIdRegex = '/youtube.com\/(?:embed|v){1}\/([a-zA-Z0-9_]+)\??/i';
                            $videoIdRegex = '/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
                        } else if (strpos($link, 'youtu.be') !== false) {
                            // works on:
                            // http://youtu.be/daro6K6mym8
                            $videoIdRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
                        }
                        if ($videoIdRegex !== null) {
                            if (preg_match($videoIdRegex, $link, $results)) {
                                $video_str = 'http://www.youtube.com/embed/' . $results[1];
                                $thumbnail_str = '';
                                $fullsize_str = '';
                                $video_id = $results[1];
                            }
                        }
                    }
                    // handle vimeo videos
                    else if (strpos($video, 'vimeo') !== false) {
                        if (strpos($video, 'player.vimeo.com') !== false) {
                            // works on:
                            // http://player.vimeo.com/video/37985580?title=0&amp;byline=0&amp;portrait=0
                            $videoIdRegex = '/player.vimeo.com\/video\/([0-9]+)\??/i';
                        } else {
                            // works on:
                            // http://vimeo.com/37985580
                            $videoIdRegex = '/vimeo.com\/([0-9]+)\??/i';
                        }
                        if ($videoIdRegex !== null) {
                            if (preg_match($videoIdRegex, $link, $results)) {
                                $video_id = $results[1];
                                // get the thumbnail
                                try {
//                                    $ch = curl_init('http://vimeo.com/api/v2/video/' . $video_id . '.php');
                                    //                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    //                                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                                    //                                    $a = curl_exec($ch);
                                    //                                    $hash = $a;
                                    //return $hash[0]["thumbnail_medium"];
                                    //$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$video_id.php"));
                                    //$hash = @file_get_contents('http://vimeo.com/api/oembed.json?url=http://vimeo.com/' . $video_id);
                                    //if (!empty($hash) && is_array($hash)) {
                                    //$video_str = 'http://vimeo.com/moogaloop.swf?clip_id=%s';
                                    $video_str = 'http://player.vimeo.com/video/' . $video_id;
                                    //$thumbnail_str = $hash[0]['thumbnail_small'];
                                    //$fullsize_str = $hash[0]['thumbnail_large'];
                                    //} else {
                                    // don't use, couldn't find what we need
                                    //     unset($video_id);
                                    // }
                                } catch (Exception $e) {
                                    unset($video_id);
                                }
                            }
                        }
                    }
                    // check if we have a video id, if so, add the video metadata
                    if (!empty($video_id)) {
                        // add to return
                        $videos[] = array(
                            'url' => sprintf($video_str, $video_id),
                            // 'thumbnail' => sprintf($thumbnail_str, $video_id),
                            /// 'fullsize' => sprintf($fullsize_str, $video_id)
                            'thumbnail' => '',
                            'fullsize' => '',
                        );
                    }
                }
            }
        }
        // return array of parsed videos
        return $videos;
    }

    /**
     * save post rating
     *
     * @return \Illuminate\Http\Response
     */
    public function savePostRating(Request $request)
    {
        $request_data = $request->all();
        $grah_cret = PostGraphResult::insert($request_data['request']);

        $contention_data = '';
        if (isset($request_data['post_contention']) && !empty($request_data['post_contention'])) {
            $data = array();
            $data['post_id'] = $request_data['post_id'];
            $data['user_id'] = Auth::user()->id;
            $data['contention'] = $request_data['post_contention'];
            $data['created_at'] = time();
            $data['updated_at'] = time();

            $contention_data = PostContention::create($data);
        }

        $post = Post::where('id', $request_data['post_id'])->first();
        // dd($post);
        if ($contention_data || $grah_cret) {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Rating submitted successfully.');
            return Redirect::route('post.post-view', $post->slug);
        } else {
            $request->session()->flash('message.level', 'error');
            $request->session()->flash('message.content', 'Rating has not saved, please try again.');
            return Redirect::route('post.post-view', $post->slug);
        }
    }

    /**
     * Get page data
     *
     * @return \Illuminate\Http\Response
     */
    public function getPageData($version, $slug)
    {
        $v = explode("v", $version);
        $post = Page::where('meta_key', $slug)
            ->where('version', $v[1])
            ->first();
        return view('pages/page', ['post' => $post]);
    }

    /**
     * Get Term condition and privacy policy page
     *
     * @return \Illuminate\Http\Response
     */
    public function getUpdatePolicyData()
    {
        $term_conditions = Page::where('meta_key', 'term')->latest()->first();
        $policy = Page::where('meta_key', 'privacy_policy')->latest()->first();
        return view('auth.update_term_policy', array('term' => $term_conditions, 'policy' => $policy));
    }

    /**
     * Contact Us page
     *
     * @return \Illuminate\Http\Response
     */
    public function contactUsPage(Request $request)
    {
        return view('pages/contact');
    }

    /**
     * send contact detail to admin
     *
     * @return \Illuminate\Http\Response
     */
    public function sendContact(Request $request)
    {
        $request_data = $request->all();

        $name = $request_data['name'];
        $email = $request_data['email'];
        $subject = $request_data['subject'];
        $message = $request_data['message'];
        $admin_email = \Config::get('variable.ADMIN_EMAIL');

        Mail::send('emails.contact_us', ['data' => array("name" => $name, "email" => $email, 'subject' => $subject, 'message' => $message)], function ($message) use ($name, $email, $subject, $admin_email) {
            $message->to($admin_email);
            $message->from(trim($email), $name)->subject('Contact Us - ' . $subject);
        });

        Mail::send('emails.contact_us_confirmation', ['data' => array("name" => $name, "email" => $email, 'subject' => $subject, 'message' => $message)], function ($message) use ($name, $email, $subject, $admin_email) {
            $message->to($email);
            $message->from(trim($admin_email), 'Thinkcitizen')->subject('Contact Us - Confirmation');
        });

        if (count(Mail::failures()) > 0) {
            $request->session()->flash('message.level', 'error');
            $request->session()->flash('message.content', 'Mail has not sent, please try again.');
            return Redirect::route('getpage.contact');
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Thank you for contacting us we will get back to you shortly.');
            return Redirect::route('getpage.contact');
        }
    }

    public function getUnsubscribe()
    {
        return view('auth.unsubscribe');
    }

    /*
     * Main Function for unsubscribe user
     * @param Request $request (email)
     * @return type (status, success/error)
     */

    public function unsubscribe(Request $request)
    {

        $user = UserSubscription::where('email', $request->email)->first();

        if ($user) {
            $user->un_subscribe = time();
            $user->status = 0;

            $user->save();

            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'You have successfully unsubscribed the newsletter.');
            return Redirect('/');
        } else {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Your email id is not registered with as subscriber.');

            return Redirect::back();
        }
    }

    public function subscriptionReminderCron(Request $request)
    {
        $next_five_days = strtotime("+5 day");
        $next_five_date = date('y-m-d', $next_five_days);
        $users = Subscription::select(DB::raw('GROUP_CONCAT(user_id) as user_id'))->whereRaw("DATE_FORMAT(FROM_UNIXTIME(plan_end_date), '%y-%m-%d') ='" . $next_five_date . "'")
            ->where('status', 1)
            ->first()->toArray();
        $user_id = [];
        $user_id = explode(',', $users["user_id"]);

        $user_emails = User::select(DB::raw('GROUP_CONCAT(email) as email'))
            ->whereIn('id', $user_id)
            ->first()->toArray();
        $final_user_emails = [];
        $final_user_emails = explode(',', $user_emails["email"]);
        //dd($user_emails["email"]);
        if ($user_emails["email"] != null) {
            try {
                $project_name = \Config::get('app.name');
                $info_url = \Config::get('variable.INFO_MAIL');
//                $info_url = 'manish@ignivasolutions.com';
                $admin_email = \Config::get('variable.ADMIN_EMAIL');
                Mail::send('emails.payment_reminder', ['data' => array()], function ($message) use ($admin_email, $final_user_emails, $project_name, $info_url) {
                    //$message->to($info_url, $project_name);
                    $message->bcc($final_user_emails);
                    $message->from(trim($info_url))->subject('ThinkCitizen - Subscription Reminder');
                });

                Mail::send('emails.admin_payment_reminder_confirm', ['data' => array('users' => $final_user_emails)], function ($message) use ($admin_email, $final_user_emails, $project_name, $info_url) {
                    $message->to($admin_email, $project_name);
                    $message->from(trim($info_url))->subject('ThinkCitizen - Subscription Reminder Confirmation');
                });
            } catch (Exception $e) {

            }
        } else {
            die("No email has found!");
        }
    }

}
