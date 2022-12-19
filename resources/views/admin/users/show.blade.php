<div class="modal-header">
    <h4 class="modal-title">User Details</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">
    <div class="card-block groups-contact">
        <h4>About {{$user->name}}</h4>
        <ul class="list-group">
            <li class="list-group-item justify-content-between">
                Name:
                <span class=" ">{{$user->name}}</span>
            </li>
            <li class="list-group-item justify-content-between">
                Email:
                <span class="">{{$user->email}}</span>
            </li>
            <li class="list-group-item justify-content-between">
                Phone:
                <span class="">{{$user->phone}}</span>
            </li>
            <li class="list-group-item justify-content-between">
                Status:
                <span class="">{{$user->approval_status}}</span>
            </li>
        </ul>

        <hr>
        <form class="" method="post" action="{{route('users.store')}}">
            @csrf
            <input type="hidden" name="id" value="{{$user->id}}">
            <div class="row">
                <div class="col">
                    <label for="status">User Status:</label>
                </div>
                <div class="col">
                    <select name="status" id="userStatus" class="form-control" required>
                        <option value="">-- Select One --</option>
                        <option value="0" @if($user->status ==0 ) selected @endif>Rejected</option>
                        <option value="1" @if($user->status ==1) selected @endif>Approved</option>
                        <option value="2" @if($user->status==2) selected @endif>Pending</option>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>
    </div>

</div>

<div class="modal-footer">

    {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
</div>