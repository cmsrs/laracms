<!doctype html>
<html>
<head>
   @include('laracms::includes.head')
   @includeIf('laracms::includes.mysite')
</head>
<body>

   <div id="appall" >
      

      @include('laracms::includes.header')

      <main role="main" class="container pt-5 mb-5">
      @yield('content')
      </main><!-- /.container front -->
   </div>


   <script type="application/javascript" src="/js/lib/vue.js"></script>
   <script type="application/javascript" src="/js/lib/axios.js"></script>
   <script type="application/javascript" src="/js/cmsrs.js"></script>  


   <?php if( 'gallery' == $view ){ ?>
      <script type="application/javascript">
         var imagesGlobal = JSON.parse('<?php echo json_encode($page->arrImages($lang)) ?>');    
      </script>
      <script type="application/javascript" src="/js/gallery.js"></script>  
   <?php } ?>


   <?php if( 'cms' == $view ){ ?>      
      @if( ($page->type == 'contact') &&  $re_public )
         <script src="https://www.google.com/recaptcha/api.js?render={{ $re_public }}"></script>
         <script>
            var rePublic = '{{ $re_public }}';
         </script>
      @endif      
   <?php } ?>

   <?php if( 'shoppingsuccess' == $view ){ ?>
      <script>
         localStorage.removeItem('cart');
         //localStorage.clear();
      </script>
   <?php } ?>


   @include('laracms::includes.footer')
   @includeIf('laracms::includes.mysitefooter')   
</body>
</html>
