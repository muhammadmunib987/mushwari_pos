<?php

namespace Modules\Superadmin\Http\Controllers;

use Log;
use App\User;
use App\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Superadmin\Entities\SuperadminCommunicatorLog;
use Modules\Superadmin\Notifications\SuperadminCommunicator;

class CommunicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $businesses = Business::orderby('name')
                                ->pluck('name', 'id');

        return view('superadmin::communicator.index')
                ->with(compact('businesses'));
    }

    /**
     * Sends notification to the required business owners.
     *
     * @param  Request  $request
     * @return Response
     */
public function send(Request $request)
{
    if (!auth()->user()->can('superadmin')) {
        abort(403, 'Unauthorized action.');
    }

    if (config('app.env') == 'demo') {
        return back()->with('status', [
            'success' => 0,
            'msg' => 'Feature disabled in demo!!',
        ]);
    }

    $input = $request->input();
    $business_id = session()->get('user.business_id');
    $business = Business::findOrFail($business_id);
    $sms_settings = $business->sms_settings;
    $plainMessage = strip_tags($input['message']);

    $business_owners = User::join('business as B', 'users.id', '=', 'B.owner_id')
        ->whereIn('B.id', $input['recipients'])
        ->select('users.*')
        ->groupBy('users.id')
        ->get();

    foreach ($business_owners as $user) {
        $mobile = $user->contact_no ?? null;

        // Send SMS/WhatsApp
        if ($mobile) {
            if (
                !empty($sms_settings['enable_gezya']) &&
                $sms_settings['enable_gezya'] == 1 &&
                !empty($sms_settings['gezya_sms'])
            ) {
                $this->sendViaGezya($mobile, $plainMessage, $sms_settings['gezya_sms']);
            }

            if (
                !empty($sms_settings['enable_whatsapp']) &&
                $sms_settings['enable_whatsapp'] == 1 &&
                !empty($sms_settings['whatsapp_api_key']) &&
                !empty($sms_settings['whatsapp_account_id'])
            ) {
                $this->sendViaMushwari($mobile, $plainMessage, [
                    'api_key' => $sms_settings['whatsapp_api_key'],
                    'account_id' => $sms_settings['whatsapp_account_id'],
                ]);
            }
        }

        // Validate and send email notification individually
        $email = strtolower($user->email);

        // Check email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            \Log::warning("Invalid email format: {$email}");
            continue;
        }

        // Optional: Check domain MX record
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, "MX")) {
            \Log::warning("Email domain does not exist or can't receive emails: {$email}");
            continue;
        }

        try {
            \Notification::send($user, new SuperadminCommunicator($input));
        } catch (\Exception $e) {
            \Log::warning("Notification failed for {$email}: " . $e->getMessage());
        }
    }

    SuperadminCommunicatorLog::create([
        'business_ids' => $input['recipients'],
        'subject' => $input['subject'],
        'message' => $input['message'],
    ]);

    return back()->with('status', [
        'success' => 1,
        'msg' => __('lang_v1.success'),
    ]);
}


    private function sendViaGezya($mobile, $message, $apiKey)
    {
        $url = "https://sms.gezya.com/api/sendsms.php";
        $params = http_build_query([
            'apiKey' => $apiKey,
            'sender' => 'GEZYA',
            'number' => $mobile,
            'message' => $message,
        ]);

        try {
            $client = new \GuzzleHttp\Client();
            $client->get("{$url}?{$params}");
        } catch (\Exception $e) {
            Log::error("Gezya SMS failed: " . $e->getMessage());
        }
    }

    private function sendViaMushwari($mobile, $message, $config)
    {
        try {
            $client = new \GuzzleHttp\Client();

            $client->post('https://business.mushwari.com/api/v1/send', [
                'headers' => [
                    'apiKey' => $config['api_key'],
                    'accountId' => $config['account_id'],
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'number' => $mobile,
                    'message' => $message,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error("Mushwari WhatsApp failed: " . $e->getMessage());
        }
    }

    public function getHistory()
    {
        $history = SuperadminCommunicatorLog::select('subject', 'message', 'created_at');

        return Datatables::of($history)
                         ->editColumn(
                             'created_at',
                             '{{@format_date($created_at)}} {{@format_time($created_at)}}'
                         )
                         ->rawColumns([1])
                         ->make(false);
    }
}
