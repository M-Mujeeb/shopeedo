<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use Auth;
use App\Models\ProductsImport;
use PDF;
use Excel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\HeadingRowImport;

class ProductBulkUploadController extends Controller
{
    public function index()
    {
        if (Auth::user()->shop->verification_status) {
            return view('seller.product.product_bulk_upload.index');
        } else {
            flash(translate('Your shop is not verified yet!'))->warning();
            return back();
        }
    }

    public function pdf_download_category()
    {
        $categories = Category::all();

        return PDF::loadView('backend.downloads.category', [
            'categories' => $categories,
        ], [], [])->download('category.pdf');
    }

    public function pdf_download_brand()
    {
        $brands = Brand::all();

        return PDF::loadView('backend.downloads.brand', [
            'brands' => $brands,
        ], [], [])->download('brands.pdf');
    }

    public function bulk_upload(Request $request)
    {
        // if($request->hasFile('bulk_file')){
        //     $import = new ProductsImport;
        //     Excel::import($import, request()->file('bulk_file'));
        // }

        // return back();

        $request->validate([
            'bulk_file' => 'required|file|mimes:xls,xlsx',
        ]);

        try {
            // Validate the template by checking the header row
            $headings = (new HeadingRowImport)->toArray($request->file('bulk_file'));
            $expectedHeadings = ['name', 'description', 'category_id', 'multi_categories', 'brand_id', 'video_provider', 'video_link', 'tags', 'unit_price', 'unit', 'slug', 'current_stock', 'est_shipping_days', 'sku', 'meta_title', 'meta_description', 'thumbnail_img', 'photos']; // replace with your expected columns

            // Check if the headings match the expected format
            if ($headings[0][0] !== $expectedHeadings) {
                return back()->withErrors(['bulk_file' => 'The uploaded file does not match the required template.']);
            }

            // Proceed with importing the file if validation passes
            $import = new ProductsImport;
            Excel::import($import, $request->file('bulk_file'));

            return back();
        } catch (ValidationException $e) {
            // Handle validation errors during the import
            $failures = $e->failures();
            $errorMessage = "The uploaded file contains errors.";

            foreach ($failures as $failure) {
                $errorMessage .= " Error on row " . $failure->row() . ": " . implode(", ", $failure->errors());
            }

            return back()->withErrors(['bulk_file' => $errorMessage]);
        } catch (\Exception $e) {
            // Handle any other errors
            return back()->withErrors(['bulk_file' => 'An error occurred while processing the file: ' . $e->getMessage()]);
        }
    }
}
