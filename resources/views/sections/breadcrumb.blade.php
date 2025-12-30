@if(!isset($hidePageHeader))
<section class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">
            <div class="col-5">
                <a href="#" onclick="location.reload();"><h1 class="mb-xs-2" style="color:#000;text-transform: capitalize;text-decoration: underline"> {!! $pageTitle !!}</h1></a>
            </div>
            <div class="col-7 text-right">
                <span class="float-sm-right">@yield('create-button')</span>
            </div>
        </div>
    </div>
</section>
@endif