<?php

namespace App\Http\Controllers;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Category;
use App\Models\AttributeValue;
use App\Models\Cart;
use App\Models\ProductCategory;
use App\Models\Wishlist;
use App\Models\User;
use App\Notifications\ShopProductNotification;
use Carbon\Carbon;
use CoreComponentRepository;
use Artisan;
use Cache;
use App\Services\ProductService;
use App\Services\ProductTaxService;
use App\Services\ProductFlashDealService;
use App\Services\ProductStockService;
use App\Services\FrequentlyBoughtProductService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use App\Mail\SecondEmailVerifyMailManager;
use Mail;
use App\Services\MailjetAuthMailer;

class ProductController extends Controller
{
    protected $productService;
    protected $productTaxService;
    protected $productFlashDealService;
    protected $productStockService;
    protected $frequentlyBoughtProductService;

    public function __construct(
        ProductService $productService,
        ProductTaxService $productTaxService,
        ProductFlashDealService $productFlashDealService,
        ProductStockService $productStockService,
        FrequentlyBoughtProductService $frequentlyBoughtProductService
    ) {
        $this->productService = $productService;
        $this->productTaxService = $productTaxService;
        $this->productFlashDealService = $productFlashDealService;
        $this->productStockService = $productStockService;
        $this->frequentlyBoughtProductService = $frequentlyBoughtProductService;

        // Staff Permission Check
        $this->middleware(['permission:add_new_product'])->only('create');
        $this->middleware(['permission:show_all_products'])->only('all_products');
        $this->middleware(['permission:show_in_house_products'])->only('admin_products');
        $this->middleware(['permission:show_seller_products'])->only('seller_products');
        $this->middleware(['permission:product_edit'])->only('admin_product_edit', 'seller_product_edit');
        $this->middleware(['permission:product_duplicate'])->only('duplicate');
        $this->middleware(['permission:product_delete'])->only('destroy');
        $this->middleware(['permission:set_category_wise_discount'])->only('categoriesWiseProductDiscount');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_products(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;

        $products = Product::where('added_by', 'admin')->where('auction_product', 0)->where('wholesale_product', 0);

        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%')
                ->orWhereHas('stocks', function ($q) use ($sort_search) {
                    $q->where('sku', 'like', '%' . $sort_search . '%');
                });
        }

        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);


        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller_products(Request $request, $product_type)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('added_by', 'seller')->where('auction_product', 0)->where('wholesale_product', 0);
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        $products = $product_type == 'physical' ? $products->where('digital', 0) : $products->where('digital', 1);
        $products = $products->orderBy('created_at', 'desc')->paginate(15);
        $type = 'Seller';

        if ($product_type == 'digital') {
            return view('backend.product.digital_products.index', compact('products', 'sort_search', 'type'));
        }


        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }

    public function all_products(Request $request)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('auction_product', 0)->where('wholesale_product', 0);
        if (get_setting('vendor_system_activation') != 1) {
            $products = $products->where('added_by', 'admin');
        }
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%' . $sort_search . '%')
                ->orWhereHas('stocks', function ($q) use ($sort_search) {
                    $q->where('sku', 'like', '%' . $sort_search . '%');
                });
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->orderBy('created_at', 'desc')->paginate(15);
        $type = 'All';

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        CoreComponentRepository::initializeCache();

        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.create', compact('categories'));
    }

    public function add_more_choice_option(Request $request)
    {
        $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();

        $html = '';

        foreach ($all_attribute_values as $row) {
            $html .= '<option value="' . $row->value . '">' . $row->value . '</option>';
        }

        echo json_encode($html);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = $this->productService->store($request->except([
            '_token',
            'sku',
            'choice',
            'tax_id',
            'tax',
            'tax_type',
            'flash_deal_id',
            'flash_discount',
            'flash_discount_type'
        ]));
        $request->merge(['product_id' => $product->id]);

        //Product categories
        $product->categories()->attach($request->category_ids);

        //VAT & Tax
        if ($request->tax_id) {
            $this->productTaxService->store($request->only([
                'tax_id',
                'tax',
                'tax_type',
                'product_id'
            ]));
        }

        //Flash Deal
        $this->productFlashDealService->store($request->only([
            'flash_deal_id',
            'flash_discount',
            'flash_discount_type'
        ]), $product);

        //Product Stock
        $this->productStockService->store($request->only([
            'colors_active',
            'colors',
            'choice_no',
            'unit_price',
            'sku',
            'current_stock',
            'product_id'
        ]), $product);

        // Frequently Bought Products
        $this->frequentlyBoughtProductService->store($request->only([
            'product_id',
            'frequently_bought_selection_type',
            'fq_bought_product_ids',
            'fq_bought_product_category_id'
        ]));

        // Product Translations
        $request->merge(['lang' => env('DEFAULT_LANGUAGE')]);
        ProductTranslation::create($request->only([
            'lang',
            'name',
            'unit',
            'description',
            'product_id'
        ]));

        flash(translate('Product has been inserted successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return redirect()->route('products.admin');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin_product_edit(Request $request, $id)
    {
        CoreComponentRepository::initializeCache();

        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('admin/digitalproducts/' . $id . '/edit');
        }

        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('digitalproducts/' . $id . '/edit');
        }
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        // $categories = Category::all();
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {

        //Product
        $product = $this->productService->update($request->except([
            '_token',
            'sku',
            'choice',
            'tax_id',
            'tax',
            'tax_type',
            'flash_deal_id',
            'flash_discount',
            'flash_discount_type'
        ]), $product);

        $request->merge(['product_id' => $product->id]);

        //Product categories
        $product->categories()->sync($request->category_ids);


        //Product Stock
        $product->stocks()->delete();
        $this->productStockService->store($request->only([
            'colors_active',
            'colors',
            'choice_no',
            'unit_price',
            'sku',
            'current_stock',
            'product_id'
        ]), $product);

        //Flash Deal
        $this->productFlashDealService->store($request->only([
            'flash_deal_id',
            'flash_discount',
            'flash_discount_type'
        ]), $product);

        //VAT & Tax
        if ($request->tax_id) {
            $product->taxes()->delete();
            $this->productTaxService->store($request->only([
                'tax_id',
                'tax',
                'tax_type',
                'product_id'
            ]));
        }

        // Frequently Bought Products
        $product->frequently_bought_products()->delete();
        $this->frequentlyBoughtProductService->store($request->only([
            'product_id',
            'frequently_bought_selection_type',
            'fq_bought_product_ids',
            'fq_bought_product_category_id'
        ]));

        // Product Translations
        ProductTranslation::updateOrCreate(
            $request->only([
                'lang',
                'product_id'
            ]),
            $request->only([
                'name',
                'unit',
                'description'
            ])
        );

        flash(translate('Product has been updated successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        if ($request->has('tab') && $request->tab != null) {
            return Redirect::to(URL::previous() . "#" . $request->tab);
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->product_translations()->delete();
        $product->categories()->detach();
        $product->stocks()->delete();
        $product->taxes()->delete();
        $product->frequently_bought_products()->delete();
        $product->last_viewed_products()->delete();
        $product->flash_deal_products()->delete();

        if (Product::destroy($id)) {
            Cart::where('product_id', $id)->delete();
            Wishlist::where('product_id', $id)->delete();

            flash(translate('Product has been deleted successfully'))->success();

            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return back();
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function bulk_product_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $product_id) {
                $this->destroy($product_id);
            }
        }

        return 1;
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);

        //Product
        $product_new = $this->productService->product_duplicate_store($product);

        //Product Stock
        $this->productStockService->product_duplicate_store($product->stocks, $product_new);

        //VAT & Tax
        $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

        // Product Categories
        foreach ($product->product_categories as $product_category) {
            ProductCategory::insert([
                'product_id' => $product_new->id,
                'category_id' => $product_category->category_id,
            ]);
        }

        // Frequently Bought Products
        $this->frequentlyBoughtProductService->product_duplicate_store($product->frequently_bought_products, $product_new);

        flash(translate('Product has been duplicated successfully'))->success();
        if ($request->type == 'In House')
            return redirect()->route('products.admin');
        elseif ($request->type == 'Seller')
            return redirect()->route('products.seller');
        elseif ($request->type == 'All')
            return redirect()->route('products.all');
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        $product->save();
        Cache::forget('todays_deal_products');
        return 1;
    }

    public function updatePublished(Request $request)
    {
        $request->validate([
            'reason' => 'max:255'
        ]);
        $baseUrl = config('app.url') . '/public/assets/documents/seller/PrivacyandConfidentiality.pdf';


        $product = Product::findOrFail($request->id);

        if ($request->reason) {
            // dd($product->user);
            $array['view'] = 'emails.verification';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = translate('Important Notice: Product Deactivation on Shopeedo  ');
            $array['content'] = 'Dear ' . $product->user->name . ',<br></br>

            We hope this email finds you well. As part of our ongoing efforts to maintain the highest quality
            standards on Shopeedo, we periodically review and update our product listings. After a recent
            review, we regret to inform you that some of your products have been flagged for deactivation due to ' . $request->reason . '.<br>
            <strong>Deactivated Products:</strong> <br><br>' . $product->name . '<br><br>
            <strong>Reason for Deactivation:</strong>' . $request->reason . '. <br><br>
            We understand the impact this may have on your business, and we are committed to working
            with you to resolve any issues. To prevent future deactivations, we recommend reviewing our ' . $baseUrl . '
            and ensuring all your products comply with these standards.<br><br>
            If you believe this deactivation was made in error or if you have addressed the issues outlined,
            please contact our support team at <a target="_blank">support@shopeedo.com</a> ] within the next 7 days for a review.
            We are here to assist you and ensure a smooth selling experience on Shopeedo.<br><br>
            Thank you for your understanding and cooperation.<br><br>
            <strong>Best Regards,</strong><br><br>
            The Shopeedo Team
            ';

            Mail::to('sabtain.ali@logiqon.co')->queue(new SecondEmailVerifyMailManager($array));



            $product->published = $request->status;
            $product->unpublished_reason = $request->reason;
        } else {
            $product->unpublished_reason = '';
            $product->published = $request->reason;
        }




        if ($product->added_by == 'seller' && addon_is_activated('seller_subscription') && $request->status == 1) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }

        $product->save();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        return 1;
    }

   public function updateProductApproval(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->approved = $request->approved;
        $seller = User::where('id', $product->user_id)->first();
        if ($product->added_by == 'seller' && addon_is_activated('seller_subscription')) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }
        if($request->approved == 1){
            try{
                $mailjet = new MailjetAuthMailer();
                $templateId = env('MAILJET_TEMPLATE_SELLER_PRODUCT_APPROVAL');

                 $array = [
                'to' => $seller->email,
                'subject' => translate('Product Approval Notification – Your Products are Now Live on Shopeedo'),
                'template_id' => $templateId,
                'variables' => [
                    'seller_name' => $seller->name,
                    'product_name' =>$product->name,
                ],
                'view' => 'emails.verification',
                'content' => '
            Dear,<br><br>
    
        We are pleased to inform you that your product submissions have been reviewed and approved. Your products are now live and available for purchase on Shopeedo!<br><br>
    
        <strong>Approved Products:</strong><br>
        ● ' . $product->name . '<br>
       <br>
    
        <strong>What’s Next?</strong><br>
        1. <strong>Monitor Your Listings:</strong> Regularly check your product listings to ensure they remain accurate and up-to-date. Pay attention to inventory levels, pricing, and any customer feedback.<br>
        2. <strong>Optimize for Success:</strong> Utilize Shopeedo’s seller tools to enhance your listings. Consider adding more detailed descriptions, high-quality images, and relevant keywords to improve search visibility and attract more customers.<br>
        3. <strong>Promotions and Advertising:</strong> Take advantage of our promotional tools and advertising options to boost the visibility of your products. Special offers, discounts, and targeted ads can significantly increase your sales.<br>
        4. <strong>Customer Engagement:</strong> Engage with your customers promptly. Respond to inquiries, manage orders efficiently, and handle any issues with care. Positive customer interactions lead to repeat business and positive reviews.<br><br>
    
        <strong>Need Assistance?</strong><br>
        Our support team is available to help you with any questions or concerns you may have. Please reach out to us at support@shopeedo.com or visit our Help Center for more information and resources.<br><br>
    <div style="text-align:center">
    <p style="color:blue">
    We are excited to have your products featured on Shopeedo and look forward to seeing your business succeed. Thank you for choosing Shopeedo as your e-commerce partner.
    </p>
     
    </div>
       <br><br>
    
        Best regards,<br>
        The Shopeedo Team
    '
            ];
            $response = $mailjet->send($array);

            if (!$response->success()) {
                \Log::error('Mailjet failed during Product approval: ' . $response->getReasonPhrase());
                return response()->json(['result' => false, 'message' => translate('Failed to send approval email.')]);
            }
               

            } catch (\Exception $e) {
            \Log::error('Product Approval email notification exception: ' . $e->getMessage());
            return response()->json(['result' => false, 'message' => translate('Failed to send approval email.')]);
        }
           
    
        }else if($request->approved == 0){

             try{
                $mailjet = new MailjetAuthMailer();
                $templateId = env('MAILJET_TEMPLATE_SELLER_PRODUCT_REJECTION');

                 $array = [
                'to' => $seller->email,
                'subject' => translate('Product Submission Status – ' . $product->name  . ' Rejected'),
                'template_id' => $templateId,
                'variables' => [
                    'seller_name' => $seller->name,
                    'product_name' =>$product->name,
                ],
                'view' => 'emails.verification',
                'content' => '
    Dear,<br><br>

    Thank you for your recent product submission to Shopeedo. We sincerely appreciate your interest in being a part of our platform and the effort you put into preparing your product for listing.<br><br>

    After a thorough review by our team, we regret to inform you that we are unable to approve ' . $product->name . ' for listing on Shopeedo at this time. We understand that this news might be disappointing and we would like to offer you detailed feedback to assist you in addressing the issues identified during the review process.<br><br>

    <strong>Reason for Rejection:</strong><br>
   

    <strong>Detailed Feedback:</strong><br>
    1. <strong>Image Quality:</strong> Ensure images are at least [minimum resolution] pixels and taken from multiple angles (front, side, back, top, and close-up of any important details). Use natural lighting or a lightbox to avoid shadows and reflections. Ensure the background is clean and uncluttered to highlight the product.<br>
    2. <strong>Product Description:</strong> Include detailed specifications such as size, color, material, and any unique features. Provide information on how the product can be used, its benefits, and any other relevant details. Use clear and engaging language to describe the product’s value proposition.<br>
    3. <strong>Policy Compliance:</strong> Review Shopeedo’s Product Guidelines for information on what is required for product approvals. Ensure that the product meets all legal and regulatory requirements. Verify that the product’s claims are accurate and substantiated.<br><br>

    <strong>Next Steps:</strong><br>
    1. <strong>Review and Improve:</strong> Please review the feedback provided and make the necessary improvements to your product submission. We encourage you to take the time to address each of the points mentioned to increase the likelihood of future approvals.<br>
    2. <strong>Resubmit Your Product:</strong> After making the required adjustments, you are welcome to resubmit ' . $product->name . ' for a new review. Please ensure that all aspects of your product listing meet Shopeedo’s guidelines and standards before resubmitting.<br>
    3. <strong>Seek Assistance:</strong> If you have any questions about the feedback or need further clarification, our support team is available to assist you. Feel free to reach out to us at support@shopeedo.com and we will be happy to provide additional support and guidance.<br><br>

    <strong>Resources for Improvement:</strong><br>
    ● Product Image Guidelines<br>
    ● Writing Effective Product Descriptions<br>
    ● Shopeedo Seller Policies<br><br>

    We are committed to helping you succeed on Shopeedo and look forward to the opportunity to review your revised product. Your feedback is invaluable, and we hope to see your future submissions meet our platform’s standards.<br><br>

    Thank you again for choosing Shopeedo as your e-commerce partner. We appreciate your understanding and look forward to your continued participation.<br><br>

    Best regards,<br>
    The Shopeedo Team
'
            ];
            $response = $mailjet->send($array);

            if (!$response->success()) {
                \Log::error('Mailjet failed during Product rejection: ' . $response->getReasonPhrase());
                return response()->json(['result' => false, 'message' => translate('Failed to send rejection email.')]);
            }
               

            } catch (\Exception $e) {
            \Log::error('Product Approval email notification exception: ' . $e->getMessage());
            return response()->json(['result' => false, 'message' => translate('Failed to send rejection email.')]);
        }
        }else{
            
        }
        

        $product->save();

        $users                  = User::findMany($product->user_id);
        $data = array();
        $data['product_type']   = $product->digital ==  0 ? 'physical' : 'digital';
        $data['status']         = $request->approved == 1 ? 'approved' : 'rejected';
        $data['product']        = $product;

        if($request->approved == 1){
            $data['notification_type_id'] = get_notification_type('seller_product_approved', 'type')->id;
        }else{
            $data['notification_type_id'] = get_notification_type('seller_product_rejected', 'type')->id;
        }


        Notification::send($users, new ShopProductNotification($data));


        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function sku_combination(Request $request)
    {
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                if (isset($request[$name])) {
                    $data = array();
                    foreach ($request[$name] as $key => $item) {
                        // array_push($data, $item->value);
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = (new CombinationService())->generate_combination($options);
        return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                if (isset($request[$name])) {
                    $data = array();
                    foreach ($request[$name] as $key => $item) {
                        // array_push($data, $item->value);
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = (new CombinationService())->generate_combination($options);
        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }

    public function product_search(Request $request)
    {
        $products = $this->productService->product_search($request->except(['_token']));
        return view('partials.product.product_search', compact('products'));
    }

    public function get_selected_products(Request $request)
    {
        $products = product::whereIn('id', $request->product_ids)->get();
        return  view('partials.product.frequently_bought_selected_product', compact('products'));
    }

    public function setProductDiscount(Request $request)
    {
        return $this->productService->setCategoryWiseDiscount($request->except(['_token']));
    }
}