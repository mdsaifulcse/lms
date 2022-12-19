@extends('admin.layout.app')
@section('title')
    Dashboard | Admin
@endsection

@section('main-content')
    <div class="page-wrapper">
        <!-- Page-header start -->
        <div class="page-header">
            <div class="page-header-title">
                <h4> Quizzes List </h4>
                <span>There are all quiz topics and topic wise question count</span>
            </div>
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="{{url('/')}}">
                            <i class="icofont icofont-home"></i>
                        </a>
                    </li>
                    {{--<li class="breadcrumb-item">--}}
                        {{--<i class="icofont icofont-plus"></i>--}}
                        {{--<a href="{{route('users.create')}}">Create User</a>--}}
                    {{--</li>--}}
                </ul>
            </div>
        </div>
        <!-- Page-header end -->
        <!-- Page-body start -->
        <div class="page-body">
            <!-- Table header styling table start -->
            <div class="card">

                <div class="card-block table-border-style">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr class="table-primary">
                                <th>Sl</th>
                                <th>Topic</th>
                                <th>Question</th>
                            </tr>

                            </thead>
                            <tbody>
                            @forelse($quizzes as $key=>$qui)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{$qui->topic}}</td>
                                    <td>{{$qui->topic_wise_quiz_count}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <th colspan="3" class="text-center text-danger">No Data Found</th>
                                </tr>

                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Table header styling table end -->
        </div>
        <!-- Page-body end -->
    </div>


    <!-- Modal -->
    <div id="userDetailModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" id="modalContent">

            </div>

        </div>
    </div>


@endsection

@section('script')

    <script>
        $('#userDetail').on('click',function () {

            $('#userDetailModal').modal('show')
            var userId=$(this).data('userid')
            $('#modalContent').load('{{URL::to("users")}}/'+userId);

            //$('#userDetailModalBody').html('<center><img src=" {{asset('images/default/loader.gif')}}"/></center>').load('{{url('users')}}/'+userId);
        })
    </script>

@endsection