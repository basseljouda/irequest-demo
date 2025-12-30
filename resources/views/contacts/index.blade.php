@extends('layouts.app')


@section('content')


    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary">
              <div class="card-body text-center" >
                <h5 class="card-title">{{$global->company_name}}</h5>
                <p class="card-text">
                    {{$global->address}}
Tel: <a title="Call now" href="tel:{{$global->company_phone}}">{{$global->company_phone}}</a>
                </p>
              </div>
            </div><!-- /.card -->
          </div>
    </div>
      <!-- Default box -->
      <div class="card card-solid">
        <div class="card-body pb-0">
          <div class="row d-flex align-items-stretch">
            @foreach ($users as $gmruser)
            @if ($gmruser->roles->pluck('name')=='[]') @continue @endif
              <div class="col-12 col-sm-6 col-md-4">
              <div class="card bg-white">
                  <div class="card-header text-muted border-bottom-0 grm-header" style="font-weight: 500">
                  {{ucfirst($gmruser->roles->first()->name)}}
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-10">
                      <h2 class="lead"><b>{{$gmruser->name}}</b></h2>
                      
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                          <li class="small" style="margin-bottom: 7px"><span class="fa-li"><i class="fa fa-lg fa-envelope"></i>
                              </span> Email: <span id="c_email">{{$gmruser->email}}</span></li>
                        <li class="small"><span class="fa-li"><i class="fa fa-lg fa-phone"></i>
                            </span> Phone: <span id="c_mobile">{{$gmruser->calling_code.$gmruser->mobile}}</span></li>
                      </ul>
                      <br />
                      <div>
                          <a href="{{route('contacts.create')}}" onclick="caller='{{$gmruser->calling_code.$gmruser->mobile}}'"  class="modal-link btn btn-sm">
                      <i class="fa fa-comments"></i> Text Me
                    </a>
                    <a href="{{route('contacts.create')}}" class="modal-link btn btn-sm" onclick="caller='{{$gmruser->email}}'">
                      <i class="fa fa-mail-forward"></i> Email Me
                    </a>
                  </div>
                    </div>
                    <div class="col-2 text-center">
                      <img src="{{$gmruser->getProfileImageUrlAttribute()}}" alt="" class="img-circle img-fluid">
                    </div>
                      
                  </div>
                </div>
              </div>
            </div>
              @endforeach
            
                      
                  </div>
                </div>
              
           
        <!-- /.card-body -->
        <div class="card-footer hide">
          <nav aria-label="Contacts Page Navigation">
            <ul class="pagination justify-content-center m-0">
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">4</a></li>
              <li class="page-item"><a class="page-link" href="#">5</a></li>
              <li class="page-item"><a class="page-link" href="#">6</a></li>
              <li class="page-item"><a class="page-link" href="#">7</a></li>
              <li class="page-item"><a class="page-link" href="#">8</a></li>
            </ul>
          </nav>
        </div>
        <!-- /.card-footer -->
      
      <!-- /.card -->
</div>
    

@endsection