<?php
namespace App\Http\Traits;

use Image;
use Mail;
use Response;
use App\Models\UserGenre;
use App\Models\FavAuther;
use App\Models\StoryGenre;


trait UserTrait
{
    public function userImageVersions($name,$requested_data)
    {
        $main_dir = storage_path() . '/app/public/users/'.$requested_data['data']['id'];
        $thumb_dir = storage_path() . '/app/public/users/'.$requested_data['data']['id'].'/thumb';

        if (!file_exists($thumb_dir)) {
            mkdir($thumb_dir, 0777);
            chmod($thumb_dir, 0777);
        }
        if (file_exists($main_dir . '/' . $name)) {
            chmod($main_dir . '/' . $name, 0777);
            Image::make($main_dir . '/' . $name)->resize(110, 110)->save($thumb_dir . '/' . $name);
            chmod($thumb_dir . '/' . $name, 0777);
        }
        return $name;
    }

    private function getVerificationCode($length = 12)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }



    /* function to send a contact us email to admin */
    private function sendMail($data) {
        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'from' => \Config::get('variable.ADMIN_EMAIL'),
            'to' =>trim($data['data']['email']),
            'from_name' =>'Episodic' ,
            'subject' =>  'Episodic',
            'view' => 'mail.page_email',
            'name' => 'Episodic',
            'data' => $data["page_data"]
        );
        #Send Verification Email
        return $this->sendEmail($email_array);  #Send Verification Email                
    }


        /* function to send a contact us email to admin */
        private function sendMailDeleteUser($data) {
            #data to send in email
            $email_array = array(
                'server_url' => \Config::get('variable.SERVER_URL'),
                'from' => \Config::get('variable.ADMIN_EMAIL'),
                'to' =>trim($data['data']['email']),
                'from_name' =>'Episodic' ,
                'subject' =>  'Episodic',
                'view' => 'mail.delete_user',
                'name' => 'Episodic',
                'data' => $data["user_data"]
            );
            #Send Verification Email
            return $this->sendEmail($email_array);  #Send Verification Email                
        }


    

    /** Send Email */
    public function sendEmail($data) {
        try {
            Mail::send($data['view'], $data, function ($message) use ($data) {
                $message->to($data['to'])->from($data['from'], $data['from_name'])->subject($data['subject']);
            });
        } catch (Exception $ex) {
            return false;
        }
        if (count(Mail::failures()) > 0) {
            return false;
        } else {
            return true;
        }
    }


        // save genres

    private function addGenres($requested_data,$type='user')
    {
        if(!is_array($requested_data['genre_ids'])){
            $requested_data['genre_ids'] = explode(",",$requested_data['genre_ids']);  
        }

        if($type=='story'){
            StoryGenre::where('user_id',$requested_data['data']['id'])->delete();
        } else{
            UserGenre::where('user_id',$requested_data['data']['id'])->delete();
        }

        foreach ($requested_data['genre_ids'] as $key => $value) {
            $data[$key]['user_id'] = $requested_data['data']['id'];
            $data[$key]['created_at'] = time();
            $data[$key]['genre_id'] = $value;
            $data[$key]['story_id'] = $requested_data['story_id'];

        }
        
        if($type=='story'){
            StoryGenre::insert($data);
        } else{
            UserGenre::insert($data);
        }
        $data = \Config::get('success.success');     # success results
        return Response::json($data);
    }

    // save fav artsi
    private function addFavAuther($requested_data)
    {
        if(!is_array($requested_data['user_ids'])){
            $requested_data['user_ids'] = explode(",",$requested_data['user_ids']);  
        }
        FavAuther::where('user_id',$requested_data['data']['id'])->delete();
        foreach ($requested_data['user_ids'] as $key => $value) {
            $data[$key]['user_id'] = $requested_data['data']['id'];
            $data[$key]['created_at'] = time();
            $data[$key]['fav_user_id'] = $value;
        }
        FavAuther::insert($data);
        $data = \Config::get('success.success');     # success results
        return Response::json($data);
    }



    // uplaod file
    private function uploadFile($requested_data,$path='app/public/snopsis/user'){
        $file = $requested_data['synopsis'];
        $dynamic_name = $this->imageDynamicName();
        $filename = time() . '-' . $dynamic_name . '.' . $file->getClientOriginalExtension();  #get Dynamic Name
        $destinationPath = storage_path('app/public/users/'.$requested_data['data']['id']);      #file Path
        $file->move($destinationPath, $filename);  #Move file into folder
        return $filename;
    }




}
