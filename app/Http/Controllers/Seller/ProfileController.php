<?php

namespace App\Http\Controllers\Seller;

use App\Http\Requests\SellerProfileRequest;
use App\Models\User;
use Auth;
use Hash;
use App\Services\MailjetAuthMailer;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        return view('seller.profile.index', compact('user', 'addresses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SellerProfileRequest $request, $id)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;

        $user->ntn = $request->ntn_no;

        $user->cnic_no = $request->cnic_no;
        $user->date_of_issue = $request->date_of_issue;
        $user->date_of_expiry = $request->date_of_expiry;
        $user->front_side_picture = $request->front_side_picture;
        $user->back_side_picture = $request->back_side_picture;




        if ($request->new_password != null && ($request->new_password == $request->confirm_password)) {
            $user->password = Hash::make($request->new_password);
        }

        $user->avatar_original = $request->photo;

        $shop = $user->shop;
        if($shop->bank_name !== $request->bank_name || $shop->bank_acc_name !== $request->bank_acc_name || $shop->bank_acc_no !== $request->bank_acc_no){
            try{
                $mailjet = new MailjetAuthMailer();

        $templateId = env('MAILJET_TEMPLATE_SELLER_BANK_DETAILS');
        $name = $user->name ?? ucfirst($request->user_type);

        $array = [
            'to' => $user->email,
            'subject' => "Important: Update Required for Your Bank Account Details",
            'template_id' => $templateId,
            'variables' => [
                'seller_name' => $name,
                'seller_account_name' => $request->bank_acc_name,
            ],
            'view' => 'emails.verification',
            'content' => "
                We have noticed that there are changes or updates needed for the bank account details associated with your Shopeedo seller account. Accurate bank account information is crucial for ensuring that your payments and transactions are processed smoothly.
                <br><br>
                <strong style='color:#7D9A40'>For your security, do not share this code with anyone.</strong><br><br>
                If you did not request this code, you can safely ignore this email.<br><br>
                Best regards,<br>The Shopeedo Team
            "
        ];

        $response = $mailjet->send($array);
            

        if (!$response->success()) {
            \Log::error('Mailjet failed: ' . $response->getReasonPhrase());
           flash(translate('Failed to Send mail!'))->error();
            return back();
        }

            }catch(\Exception $e){
            \Log::error('Bank details update exception (Notification): ' . $e->getMessage());
            flash(translate('Failed to Send mail!'))->error();
            return back();
            }
        }

        if ($shop) {
            $shop->cash_on_delivery_status = $request->cash_on_delivery_status;
            $shop->bank_payment_status = $request->bank_payment_status;
            $shop->bank_name = $request->bank_name;
            $shop->bank_acc_name = $request->bank_acc_name;
            $shop->bank_acc_no = $request->bank_acc_no;
            // $shop->bank_routing_no = $request->bank_routing_no;
            $shop->save();
        }

        $user->save();

        

        flash(translate('Your Profile updated successfully!'))->success();
        return back();
    }
}
