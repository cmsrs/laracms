<?php $manyLangs = ( 1 < count($langs)); ?>
<?php $bg = env('DEMO_STATUS') ?  'bg-dark' : 'bg-secondary'; ?>
<?php //$bg = 'bg-secondary'; ?>
<?php $pLogin = Cmsrs\Laracms\Models\Page::getFirstPageByType('login');  ?>
<?php $pRegister = Cmsrs\Laracms\Models\Page::getFirstPageByType('register');  ?>
<?php $pHome = Cmsrs\Laracms\Models\Page::getFirstPageByType('home');  ?>
<?php 
  $mainPage = Cmsrs\Laracms\Models\Page::getFirstPageByType('main_page');  
  $urlMainPage = '/';
  if($mainPage){
    $urlMainPage = $mainPage->getUrl($lang);  
  }
?>
<?php $productNameSlug = !empty($product_name_slug) ? $product_name_slug : null ?>

<div id="page_id" data-page-id="{{$page ? $page->id : ''}}"></div>  
<div id="lang" data-lang="{{$lang ?  $lang : ''}}"></div>    
<div id="is_shop" data-is-shop="{{ env('IS_SHOP'), 0 }}"></div>          
<div id="commented" data-commented="{{ $page ? $page->commented : '' }}"></div>          



<nav class="navbar navbar-expand-lg navbar-dark  {{ $bg }} fixed-top lead">
    <a class="navbar-brand" href="{{ url($urlMainPage) }}">

      <?php
          $path = public_path('images/mysite/logo.png');
          $isExists = file_exists($path);
          if($isExists){
      ?>    
        <img id="logo_cmsrs" src="/images/mysite/logo.png" alt="{{ config('app.name', 'cmsRS') }}" />
      <?php }else{ ?>
        <img id="logo_cmsrs" src="/images/cms/logo_cmsrs.png" alt="{{ config('app.name', 'cmsRS') }}" />
      <?php } ?>

    </a>



  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">

      <ul class="nav-main-rs  navbar-nav mr-auto">
          <?php foreach ($menus as $menu) { ?>
            <?php $pagesPublishedAndAccess = $menu->pagesPublishedAndAccess()->get(); ?>    
            <li class="nav-item dropdown">
            <?php if( 1 == $pagesPublishedAndAccess->count() ){  ?>
              <a class=" ml-3 nav-link" href="{{$pagesPublishedAndAccess->first()->getUrl($lang)}}">{{ $pagesPublishedAndAccess->first()->translatesByColumnAndLang( 'short_title', $lang ) }}</a>
            <?php }else{ ?>
              <a class="nav-link dropdown-toggle ml-3" href="#" id="dropdown{{ $menu->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $menu->translatesByColumnAndLang( 'name', $lang ) }}</a>
              <div class="dropdown-menu" aria-labelledby="dropdown{{ $menu->id }}">
                  <?php foreach ($menu->pagesPublishedTree($pagesPublishedAndAccess) as $pageMenu) { ?>                                
                    <a class="dropdown-item" href="{{$pageMenu->getUrl($lang)}}">{{ $pageMenu->translatesByColumnAndLang( 'short_title', $lang ) }}</a>
                    <?php if( !empty($pageMenu['children']) && !empty($pageMenu->published) ){ ?>
                        <?php foreach ($pageMenu['children'] as $p) { ?>                    
                            <a class="dropdown-item ml-3" href="{{$p->getUrl($lang)}}">{{ $p->translatesByColumnAndLang( 'short_title', $lang ) }}</a>
                        <?php } ?>
                    <?php } ?>                  
                  <?php } ?>
                </div>
            <?php } ?>    
            </li>
          <?php } ?>
      </ul>

  </div>

    <ul class="list-unstyled  m-0 p-0">
      <?php if(  env('IS_SHOP', false) ){ ?>
        <li class="nav-item ml-1  mr-4 cursor-pointer" v-on:click="toglebasket()">
          <i style="font-size:40px;color:#ff5050" class="fa">&#xf07a;</i>
          <span style="color:#ff5050">@{{ cart_length ? cart_length : '' }}</span>
        </li>
        
        <div class="p-4 mr-3" id="appbasket" style="display: none" >
          <div v-if="cart_length === 0" >{{ __('There are no products in the cart') }}</div>
          <div v-if="cart_length !== 0" >{{ __('SHOPPING CART') }}</div>          
          <ul>
            <li class="mt-2" v-for="item in cart" v-bind:key="item.id">              
              <div>@{{ item.name }}</div>
              <span>@{{ item.price /100 }} zł x @{{ item.qty }}</span>
              <button class="btn" v-on:click="increment(item)">+</button>
              <button class="btn" v-on:click="decrement(item)">-</button>
            </li>
          </ul>

          <div v-if="cart.length">
            <div class="ml-4 cart-total">{{ __('Total') }}: @{{ total_sanit }} zł</div>
            <button class="ml-4 btn" v-on:click="pay()">{{ __('Pay') }}</button>
          </div>      
        </div>
          
      <?php } ?>
    </ul>


    <ul class="nav navbar-nav ml-auto" >
      <!-- Authentication Links -->
      <?php if($pLogin){ ?>
        <?php $loginStyle = $manyLangs ? 'mr-4' : ''; ?>          
        @guest            
              <li class="nav-item {{$loginStyle}}">
                  <a class="nav-link" href="{{ $pLogin->getUrl($lang) }}">{{ $pLogin->translatesByColumnAndLang( 'short_title', $lang ) }}</a>
              </li>
              @if (Route::has('register') && $pRegister )
                  <li class="nav-item  {{$loginStyle}}">
                      <a class="nav-link" href="{{ $pRegister->getUrl($lang) }}">{{ $pRegister->translatesByColumnAndLang( 'short_title', $lang ) }}</a>
                  </li>
              @endif
        @else
              @if ( $pHome )
              <li class="nav-item active {{$loginStyle}}">
                    <a class="nav-link" href="{{ $pHome->getUrl($lang) }}">{{ $pHome->translatesByColumnAndLang( 'short_title', $lang ) }}</a>
              </li>
              @endif
              <form id="logout-form" action="{{ route('logout') }}" method="POST" >
                                        @csrf
                                        <input type="submit" value="{{ __('Log out') }}"  class="nav-link" style="background:none; border-width:0px; cursor: pointer;" />
              </form>
        @endguest
      <?php } ?>
      <?php if( $manyLangs ) { ?>
        <li class="d-flex flex-row">
        <?php foreach($langs as $ll){  ?>
          <?php $classActive = ($ll == $lang) ? 'active' : ''; ?>
          <div class="ml-2  nav-item {{ $classActive }}">
              <a class="changelang nav-link" href="{{ route('changelang', ['lang' => $ll, 'pageId' => $page->id, 'productSlug' => ($productNameSlug ? $productNameSlug[$ll]  : null)] ) }}"><img src="/images/cms/{{ $ll }}.png" alt="{{ $ll }}" /> {{ strtoupper($ll) }}</a></div>
        <?php } ?>
        </li>
      <?php } ?>          
     </ul>


</nav>
