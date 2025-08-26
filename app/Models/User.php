<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Cart;
use App\Notifications\EmailVerificationNotification;
use App\Traits\PreventDemoModeChanges;
use Spatie\Permission\Traits\HasRoles;
use App\Models\DeliveryBoy;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens, HasRoles;


    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'address', 'city', 'postal_code', 'phone', 'country', 'provider_id', 'email_verified_at', 'verification_code','first_login', 'ntn', 'device_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }


    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function affiliate_user()
    {
        return $this->hasOne(AffiliateUser::class);
    }

    public function affiliate_withdraw_request()
    {
        return $this->hasMany(AffiliateWithdrawRequest::class);
    }

     public function shiftHistories()
{
    return $this->hasMany(\App\Models\DeliveryBoyShiftHistory::class, 'user_id');
}

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // public function shop()
    // {
    //     return $this->hasOne(Shop::class);
    // }

    public function shop()
{
    return $this->hasOne(Shop::class);
}

    public function seller()
    {
        return $this->hasOne(Seller::class);
    }


    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function seller_orders()
    {
        return $this->hasMany(Order::class, "seller_id");
    }
    public function seller_sales()
    {
        return $this->hasMany(OrderDetail::class, "seller_id");
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class)->orderBy('created_at', 'desc');
    }

    public function club_point()
    {
        return $this->hasOne(ClubPoint::class);
    }

    public function customer_package()
    {
        return $this->belongsTo(CustomerPackage::class);
    }

    public function customer_package_payments()
    {
        return $this->hasMany(CustomerPackagePayment::class);
    }

    public function customer_products()
    {
        return $this->hasMany(CustomerProduct::class);
    }

    public function seller_package_payments()
    {
        return $this->hasMany(SellerPackagePayment::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function affiliate_log()
    {
        return $this->hasMany(AffiliateLog::class);
    }

    public function product_bids()
    {
        return $this->hasMany(AuctionProductBid::class);
    }

    public function product_queries(){
        return $this->hasMany(ProductQuery::class,'customer_id');
    }

    public function uploads(){
        return $this->hasMany(Upload::class);
    }

    public function userCoupon(){
        return $this->hasOne(UserCoupon::class);
    }

    public function coupons(){
        return $this->hasMany(Coupon::class);
    }
    public function deliveryBoy() {
     return $this->hasOne(DeliveryBoy::class, 'user_id','id');
    }

    public function calculateWeeklyBonus(): float
    {
        // Get weekly bonuses from the business_settings table
        $weeklyBonuses = json_decode(\DB::table('business_settings')
            ->where('type', 'weekly_bonuses')
            ->value('value'), true);

        if (!$weeklyBonuses || !is_array($weeklyBonuses)) {
            return 0; // Return 0 if no bonus rules are defined
        }

        // Sort the bonuses in descending order by deliveries (to get the highest eligible bonus first)
        usort($weeklyBonuses, fn($a, $b) => $b['deliveries'] <=> $a['deliveries']);

        // Get the number of completed rides
        $completedRides = $this->deliveryBoy->completed_rides ?? 0;

        // Find the highest eligible bonus
        foreach ($weeklyBonuses as $bonus) {
            if ($completedRides >= $bonus['deliveries']) {
                return (float) $bonus['price'];
            }
        }

        return 0; // No bonus if thresholds are not met
    }

    public function calculateOutstandingAmount(): float
    {
        $deliveryBoy = $this->deliveryBoy;

        if (!$deliveryBoy) {
            return 0;
        }

        $orderCommission = (float) $deliveryBoy->order_commission;

        // 2. Calculate the weekly bonus using the existing method
        $weeklyBonus = $this->calculateWeeklyBonus();

        $totalPayments = (float) \DB::table('delivery_boy_payments')
            ->where('user_id', $this->id)
            ->sum('payment');


        $outstandingAmount = ($orderCommission + $weeklyBonus) - $totalPayments;

        return max($outstandingAmount, 0);
    }

}
