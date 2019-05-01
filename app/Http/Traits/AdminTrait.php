<?php
namespace App\Http\Traits;
use Image;
use Mail;
use Config;
use App\User;
use Storage;
use App\Models\Review;
use App\Models\RatingSubject;

trait AdminTrait
{
    public function getVerificationCode($length = 12)
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

    // function to activate /inactivate user

    public function ActiveInactiveUser($requested_data){
        $project_name = config('variable.SITE_NAME');
        $users = User::find($requested_data["id"]);
        $admin_email = \Config::get('variable.ADMIN_EMAIL');
        if ($requested_data['status'] == 1) {
            $subject = 'Account Activated';
        } else {
            $subject = 'Account De-activated';
        }
        $name = $users->name;
        $email = $users->email;
        Mail::send('emails.admin.user_status', [
            'data' => array(
                "status" => $requested_data['status'],
                "name" => $name,
                'subject' => $subject,
                'email' => $email)],
            function ($message) use ($email, $subject, $admin_email, $name, $project_name) {
                $message->to($email, ucfirst($name));
                $message->from($admin_email, $project_name)->subject($subject);
            });
        if (count(Mail::failures()) > 0) {
            $data = \Config::get('admin_error.eamil_failed');
            return Response::json($data);
        }
      return   $users = $users->update(['status' => $requested_data['status']]);
    }

    /*
     * Funtion : User review Declined By Admin send Reason to User with Email
     * Params  : id, Reason
     */

    public function reviewRejected($requested_data) {
        #Get user data
        //$userdata = User::where('id', $requested_data['user_id'])->select('id', 'email', 'name')->first();

        $review = Review::where('id', $requested_data['review_id'])->with(['toUser' => function($query) {
                        $query->select('id', 'name', 'email');
                    },
                    'fromUser' => function($query) {
                        $query->select('id', 'name', 'email');
                    },
                    'ratingSubject' => function($query) {
                        $query->select('id', 'title');
                    }
                ])->first();

        $site_name = \Config::get('variable.SITE_NAME');
        if (!empty($review)) {
            $emailData = array(
                'to' => $review->fromUser->email, #company mail
                'from' => \Config::get('variable.ADMIN_EMAIL'),
                'subject' => ''.$site_name.': Review Rejected',
                'view' => 'emails.admin.review_declined_by_admin',
                'site_name' => $site_name,
            );
            $review->created_at = date('Y-m-d', $review->created_at);
            #send message to company
            $msg = "Your review given to " . '"' . $review->toUser->name . '"' . " on " . $review->created_at . " has been declined by admin due to following reason";
            Mail::send($emailData['view'], ['toComp' => 'yes', 'msg' => $msg, 'review' => $review, 'first_name' => $review->fromUser->name, 'reason' => $requested_data['reason'], 'url' => url(), 'frontend_url' => \Config::get('variable.FRONTEND_URL')], function ($message) use ($emailData)  {
                $message
                        ->to($emailData['to'])
                        ->from($emailData['from'], $emailData['site_name'])
                        ->subject($emailData['subject']);
            });

            $emailData['to'] = $review->toUser->email; # mail
            #send message
            $msg = "A review given to you by " . '"' . $review->fromUser->name . '"' . " on " . $review->created_at . " has been declined by admin due to following reason";
            Mail::send($emailData['view'], ['toComp' => '', 'msg' => $msg, 'review' => $review, 'first_name' => $review->toUser->name, 'reason' => $requested_data['reason'], 'url' => url(), 'frontend_url' => \Config::get('variable.FRONTEND_URL')], function ($message) use ($emailData) {
                $message
                        ->to($emailData['to'])
                        ->from($emailData['from'], $emailData['site_name'])
                        ->subject($emailData['subject']);
            });
            #Result
            if (count(Mail::failures()) > 0) {
                return 0;
            } else {
                return 1;
            }
        } return 0;
    }

}
