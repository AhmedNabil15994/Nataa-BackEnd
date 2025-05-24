<?php

namespace Modules\Apps\Http\Controllers\FrontEnd;

use Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Apps\Http\Requests\FrontEnd\ContactUsRequest;
use Modules\Apps\Notifications\FrontEnd\ContactUsNotification;
use Modules\Catalog\Repositories\FrontEnd\CategoryRepository as Category;
use Modules\Slider\Repositories\FrontEnd\SliderRepository as Slider;
use Modules\Vendor\Repositories\FrontEnd\VendorRepository as Vendor;
use Cart;

class HomeController extends Controller
{
    protected $category;
    protected $slider;
    protected $vendor;

    function __construct(Category $category, Slider $slider, Vendor $vendor)
    {
        $this->category = $category;
        $this->slider = $slider;
        $this->vendor = $vendor;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        ### Get Featured Products
        $featuredProducts = $this->category->getFeaturedProducts($request);

        ### Get Latest Offers
        $latestOffers = $this->category->getLatestOffersData($request);

        ### Get Main Categories Data
        $categories = $this->category->getMainCategoriesData($request);

        $sliders = $this->slider->getAllActive();

//        $vendors = $this->vendor->getAllActiveVendors();

        ### Get Most Selling Products Data
//        $mostSellingProducts = $this->category->getMostSellingProducts($request);

        return view('apps::frontend.index', compact(
            'featuredProducts',
            'latestOffers',
            'categories',
            'sliders',
//            'mostSellingProducts'
        ));
    }

    public function contactUs()
    {
        return view('apps::frontend.contact-us');
    }

    public function sendContactUs(ContactUsRequest $request)
    {
        Notification::route('mail', config('setting.contact_us.email'))
            ->notify((new ContactUsNotification($request))->locale(locale()));

        return redirect()->back()->with(['status' => __('apps::frontend.contact_us.alerts.send_message')]);
    }
}
