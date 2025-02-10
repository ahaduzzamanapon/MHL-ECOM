<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use App\Services\ProductService;
use App\Services\FirebaseService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\ProductOfferRequest;
use App\Http\Resources\ProductAdminResource;
use App\Http\Requests\ShippingAndReturnRequest;
use App\Http\Resources\ProductDetailsAdminResource;
use App\Http\Resources\SimpleProductDetailsResource;
use App\Http\Resources\simpleProductWithVariationCountResource;
use App\Traits\ApiRequestTrait;

class ProductController extends AdminController
{
    use ApiRequestTrait;
    public ProductService $productService;
    protected $apiRequest;

    public function __construct(ProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
        $this->apiRequest     = $this->makeApiRequest();
        $this->middleware(['permission:products'])->only('export', 'generateSku');
        $this->middleware(['permission:products_create'])->only('store', 'uploadImage');
        $this->middleware(['permission:products_edit'])->only('update');
        $this->middleware(['permission:products_delete'])->only('destroy', 'deleteImage');
        $this->middleware(['permission:products_show'])->only('show', 'shippingAndReturn');
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        //dd($request->all());
        try {
            return ProductAdminResource::collection($this->productService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductDetailsAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductDetailsAdminResource($this->productService->show($product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ProductRequest $request): \Illuminate\Http\Response|ProductAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {

        return new ProductAdminResource($this->productService->store($request));

        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ProductRequest $request, Product $product): \Illuminate\Http\Response|ProductAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductAdminResource($this->productService->update($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Product $product): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->productService->destroy($product);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function uploadImage(ChangeImageRequest $request, Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductDetailsAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductDetailsAdminResource($this->productService->uploadImage($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deleteImage(Product $product, $index): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductDetailsAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductDetailsAdminResource($this->productService->deleteImage($product, $index));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ProductExport($this->productService, $request), 'Product.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function generateSku(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response(['data' => ['product_sku' => $this->productService->generateSku()]], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function shippingAndReturn(ShippingAndReturnRequest $request, Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ProductAdminResource($this->productService->shippingAndReturn($request, $product));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function productOffer(ProductOfferRequest $request, Product $product): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ProductAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $data_offer_data = new ProductAdminResource($this->productService->productOffer($request, $product));
            $input = $request->all();
            $title = $input['discount'] . "% Discount on " . $data_offer_data->name;
            $description = "Get " . $input['discount'] . "% off on " . $data_offer_data->name . " from " . $input['offer_start_date'] . " to " . $input['offer_end_date'];
            $pushNotification = (object)[
                'title' => $title,
                'description' => $description,
                'image' => $data_offer_data->cover
            ];
            $fcmWebDeviceToken    = User::whereNotNull('web_token')->pluck('web_token')->toArray();
            $fcmMobileDeviceToken = User::whereNotNull('device_token')->pluck('device_token')->toArray();
            $fcmTokenArray = array_merge($fcmWebDeviceToken, $fcmMobileDeviceToken);
            foreach($fcmTokenArray as $fcmToken){
                $firebase      = new FirebaseService();
                $firebase->sendNotification_one($pushNotification, $fcmToken, "promotion");
            }


            return $data_offer_data;
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function purchasableProducts(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  simpleProductWithVariationCountResource::collection($this->productService->purchasableProducts());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function simpleProducts(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  simpleProductWithVariationCountResource::collection($this->productService->simpleProducts());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function get_product_by_sku(Request $request)
    {

        try {
            return  $this->productService->get_product_by_sku($request->all());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function posProduct(Product $product, Request $request): SimpleProductDetailsResource|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SimpleProductDetailsResource($this->productService->showWithRelation($product, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

}
