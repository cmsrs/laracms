<?php
namespace Cmsrs\Laracms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Product extends Model
{
    private $translate;
    private $content;

    public $productFields;

    protected $fillable = [
        'sku',
        'price',
        'published',
        'page_id'
    ];

    protected $casts = [
        'published' => 'integer',
        'price' => 'integer',
        'page_id' => 'integer'
    ];

    public function page()
    {
        return $this->hasOne(Page::class, 'id', 'page_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function translates()
    {
        return $this->hasMany(Translate::class);
    } 

    public function contents()
    {
        return $this->hasMany(Content::class);
    }        

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->translate = new Translate;
        $this->content = new Content;

        $this->productFields = [
            'id',
            'published',
            'sku',
            'price',
            'page_id'
        ];  
    }


    static public function searchProducts( $lang, $key)
    {
        return DB::select("select distinct product_id from translates where (`product_id` is not null) and (`lang` = :lang) and (`column` = 'product_name') and (`value` like  :key )", ['lang' => $lang, 'key' => '%'.$key.'%' ]);
    }

    static public function objToArray( $obj )
    {
        $out = [];
        foreach( $obj as $o ){
            $out[] = $o->product_id;
        }

        return $out;
    }

    public function wrapSearchProducts( $lang, $key)
    {
        $objProducts = Product::searchProducts( $lang, $key);
        $arrProducts = Product::objToArray( $objProducts );
        return $this->getProductsWithImagesByIds($arrProducts);
    }

    static public function getDefaultProductName($productTranslates, $lang)
    {
        //$lang = Config::getDefaultLang();

        $defaultProductName = '';


        foreach ($productTranslates as $translate) {
            if( ('product_name' == $translate['column']) && ($translate['lang']  == $lang) ){
                $defaultProductName = $translate['value'];
                break;
            }            
        }
        //dump($lang);
        //dd($productTranslates);

        return $defaultProductName;
    }


    /**
     * this function is similar to: getDataToPayment
     * 
     */
    /*
    static public function getDataToOrders( $arrCart )
    {
        $user = Auth::user();
        if( empty($user) ){
            throw new \Exception("User not auth - this exception is impossible");
        }

        $ids = array_keys($arrCart);
        $arrProducts = Product::with(['translates'])->whereIn('id', $ids)->orderBy('id', 'asc')->get()->toArray();
    
        $out = [];
        $totalAmount = 0;
        foreach($arrProducts as $arrProduct){

            $itemIn = $arrCart[$arrProduct['id']];
            $out["products"][] = [
                "name" => Product::getDefaultProductName($arrProduct['translates']),
                "unitPrice" => $arrProduct['price'],
                "quantity" => $itemIn['qty']
            ];
            $baskets[] = [
                "qty" => $itemIn['qty'],                
                "user_id" => $user->id,
                "product_id" => $arrProduct['id']
            ];

            $totalAmount += $arrProduct['price'] * $itemIn['qty'];
        }
        $out['totalAmount'] =  $totalAmount;

        return  $out;
    }
    */


    static public function getDataToPayment( $arrCart, &$baskets, &$orders = false )
    {
        /*
        $user = Auth::user();
        if( empty($user) ){
            throw new \Exception("User not auth - this exception is impossible");
        }
        */

        $ids = array_keys($arrCart);
        $arrProducts = Product::with(['translates'])->whereIn('id', $ids)->orderBy('id', 'asc')->get(); //->toArray();
    
        $out = [];
        $totalAmount = 0;
        $lang = Config::getDefaultLang();
        foreach($arrProducts as $product){

            $itemIn = $arrCart[$product->id];
            if( empty($itemIn['qty']) ){
                throw new \Exception("qty empty - something wrong");
            }

            $productName = Product::getDefaultProductName( $product->translates, $lang );
            $qty = $itemIn['qty'];            

            $out["products"][] = [
                "name" =>  $productName,
                "unitPrice" => $product->price,
                "quantity" => $qty
            ];

            if( is_array($baskets) ){
                $baskets[] = [
                    "qty" => $qty,
                    //"user_id" => $user->id,
                    "price" => $product->price,
                    "product_id" => $product->id,
                    //"checkout_id" => $checkoutId
                ];
            }
            
            if( is_array($orders) ){
                $productImage = Image::getImagesAndThumbsByTypeAndRefId('product', $product->id)->toArray();
                $orders[] = [
                    "name" =>  $productName,
                    "unitPrice" => $product->price,
                    "qty" => $qty,
                    "product_id" => $product->id,
                    "product_url" => $product->getProductUrl($lang, $productName),
                    "product_img" =>  empty($productImage[0]) ? '' : $productImage[0]['fs']['small']
                ];
            }
            
            $totalAmount += $product->price * $qty;
        }
        $out['totalAmount'] =  $totalAmount;

        return  $out;
    }

    public function checkIsDuplicateName($data, $id = '')
    {
        $out = ['success' => true ];
        $products = $this->getAllProductsWithImages();
        foreach ($products as $product) {
            if ($product['id']  == $id) {
                continue;
            }
            foreach ($product['product_name'] as $lang => $name) {
                if (empty($data['product_name']) || empty($data['product_name'][$lang])) {
                    throw new \Exception("product_name is empty - but is require");
                }
                $nameIn = Str::slug($data['product_name'][$lang], "-");
                $n = Str::slug($name, "-");

                if ($nameIn == $n) {
                    $out['success'] = false;
                    $out['error'] = "Duplicate product name: $name ($lang)";
                    break;
                }
            }
        }
        return $out;
    }



    // public function setTranslate($objTranslate)
    // {
    //     if (!empty($objTranslate)) {
    //         $this->translate = $objTranslate;
    //     }
    // }

    /**
     * use also in script to load demo (test) data
     * php artisan command:load-demo-data
     */
    public function wrapCreate($data)
    {
        $product = Product::create($data);

        if (empty($product->id)) {
            throw new \Exception("I cant get product id");
        }
        $this->createTranslate([ 'product_id' => $product->id, 'data' => $data ]);
  
        if (!empty($data['images']) && is_array($data['images'])) {
            $objImage = new Image;
            $objImage->setTranslate($this->translate);
            $objImage->createImages($data['images'], 'product', $product->id);
        }
        return $product;
    }


    public function createTranslate($dd, $create = true)
    {
        $this->translate->wrapCreate($dd, $create);
        $this->content->wrapCreate($dd, $create);        
    }

    public function wrapUpdate($data)
    {
        $res = $this->update($data);
        $this->createTranslate([ 'product_id' => $this->id, 'data' => $data ], false);
        return $res;
    }

    private function getProductDataFormat($product)
    {
        $out = [];
        foreach ($this->productFields as $field) {
            $out[$field] = $product[$field];
        }
        foreach ($product['translates'] as $translate) {
            $out[$translate['column']][$translate['lang']] = $translate['value'];
            if( 'product_name' == $translate['column']){
                $out['product_name_slug'][$translate['lang']] = Str::slug($translate['value'], '-');
            }
        }
        foreach ($product['contents'] as $translate) {
            $out[$translate['column']][$translate['lang']] = $translate['value'];
        }
        return $out;
    }


    public function getProductBySlug($productSlug, $lang)
    {
        $productOut = null;
        $products = $this->getAllProductsWithTranslates();
        foreach($products as $product){
            $arrProduct = $this->getProductDataFormat($product);
            if($productSlug ==  Str::slug($arrProduct['product_name'][$lang], '-')){
                $productOut = $product;
                break;
            }
        }

        return $productOut;
    }

    public function getCategoryUrl($lang)
    {
        return $this->page()->get()->first()->getUrl($lang);
    }

    public function getProductUrl($lang, $productName)
    {
        return $this->page()->get()->first()->getUrl($lang, Str::slug($productName, '-') );
    }    

    public function getProductUrls($productWithTranslate)
    {
        $out = array();
        //$arrProduct = $this->toArray();
        $arrProduct = $this->getProductDataFormat($productWithTranslate);
        $langs = Config::arrGetLangsEnv();

        foreach($langs as $lang){
            $out['url_category'][$lang] = $this->getCategoryUrl($lang);
            $out['url_product'][$lang] = $this->getProductUrl($lang, $arrProduct['product_name'][$lang]);
        }
        return $out;
    }

    private function getProductNameDefaultLang($arrProductFormat)
    {
        $lang = Config::getDefaultLang();
        return $arrProductFormat['product_name'][$lang];
    }

    public function getProductDataByProductArr( $product )
    {
        $arrProduct = $product->toArray();

        $out = [];
        $out = $this->getProductDataFormat($arrProduct);
        $out['product_name_default_lang'] = $this->getProductNameDefaultLang($out);

        $out['images'] = Image::getImagesAndThumbsByTypeAndRefId('product', $arrProduct['id']);
        return $out;
    }

    private function getAllProductsWithTranslates()
    {
        return Product::with(['translates', 'contents'])->orderBy('id', 'asc')->get();
    }

    public function getAllProductsWithImages( $withUrls = false )
    {
        $products = $this->getAllProductsWithTranslates();
        return $this->getAllProductsWithImagesArr($products, $withUrls);
    }

    public function getAllProductsWithImagesArr($products, $withUrls = false)
    {
        $i = 0;
        $out = [];
        foreach ($products as $product) {
            $out[$i] = $this->getProductDataByProductArr( $product );

            if($withUrls){
                $urls = $product->getProductUrls($product);
                $out[$i] = array_merge($out[$i], $urls);    
            }
            $i++;
        }
        return $out;
    }
    

    /**
     * function use on the frontend
     * it should be cached
     */
    public function getAllProductsWithImagesByLang($lang)
    {
        $products = $this->getAllProductsWithImages( true );

        //dd($products);
        $out = [];
        foreach($products as $product){
            if( !empty($product['published']) ){
                $productId = $product["id"];
                //$out[$productId]["product_id"] = $productId;
                $out[$productId]["price"] = $product["price"];
                $out[$productId]["name"] = $product["product_name"][$lang];
                $out[$productId]["url_product"] = $product["url_product"][$lang];
                if( !empty($product["images"]) && !empty($img = $product["images"]->first()) ){
                    $out[$productId]["url_image"] =  $img->fs["small"];
                }
            }
        }
        return $out;
    }

    public function getAllProductsWithImagesByLangCache($lang)
    {
        $isCache = env('CACHE_ENABLE', false);
        if ($isCache) {
            $products = cache()->remember('products_name_price_'.$lang , Carbon::now()->addYear(1), function () use ($lang)  {
                return (new Product)->getAllProductsWithImagesByLang($lang);
            });
        } else {
            $products = (new Product)->getAllProductsWithImagesByLang($lang);
        }

        return $products;    
    }

    /*
    //dont use
    public function getProductDataByProductId( $productId )
    {
        $product = Product::with(['translates', 'contents'])->where('id', $productId)->orderBy('id', 'asc')->get()->first();
        $out = $this->getProductDataByProductArr( $product );

        return $out;
    }
    */

    /**
     * It is needed for sitemap
     */
    public function getProductsUrl()
    {
        $urls = [];
        $products = Product::with(['translates', 'contents', 'page' ])->where('published', '=', 1)->orderBy('id', 'asc')->get();
        $i = 0;
        foreach ($products as $key => $product) {
            if($product['page']->published && $product->published){
                $arrProduct = $this->getProductDataFormat($product);
                $langs = Config::arrGetLangsEnv();        
                foreach($langs as $lang){
                    $urls[$i][$lang] = $product->getProductUrl($lang, $arrProduct['product_name'][$lang]);
                }
                $i++;
            }
        }        
        return $urls;
    }

    /**
     * it is needed to search
     */
    public function getProductsWithImagesByIds($ids)
    {
        $products = Product::with(['translates', 'contents'])->whereIn('id', $ids)->orderBy('id', 'asc')->where('published', '=', 1)->get(); 
        return $this->dataToRender($products);
    }


    public function getProductsWithImagesByPage($pageId)
    {
        $products = Product::with(['translates', 'contents'])->where('page_id', $pageId)->orderBy('id', 'asc')->where('published', '=', 1)->get(); //->toArray();
        return $this->dataToRender($products);
        // $i = 0;
        // $out = [];
        // foreach ($products as $key => $product) {
        //     $urls =  $product->getProductUrls($product);
        //     $out[$i] =  array_merge( $this->getProductDataByProductArr( $product ), $urls);
        //     $i++;
        // }
        // return $out;
    }

    private function dataToRender($products)
    {
        $i = 0;
        $out = [];
        foreach ($products as $key => $product) {
            $urls =  $product->getProductUrls($product);
            $out[$i] =  array_merge( $this->getProductDataByProductArr( $product ), $urls);
            $i++;
        }
        return $out;
    }

    public function delete()
    {
        foreach ($this->images()->get() as $img) {
            $img->delete();
        }
        return parent::delete();
    }
}
