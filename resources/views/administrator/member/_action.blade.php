<a href="{{ $referal_url }}" class="btn btn-xs btn-primary">List Referal</a><br>

<a href="{{ $edit_url }}" class="btn btn-xs btn-primary">Edit</a>

{!! Form::model($model,['url'=>$ubah_url,'method'=>'post']) !!}
    {!! Form::submit('Ubah', ['class'=>'btn btn-xs btn-warning ubah-status','data-confirm-message'=>$ubah_message]) !!}
{!! Form::close() !!}

{!! Form::model($model,['url'=>$revoke_url,'method'=>'post']) !!}
    {!! Form::submit('Revoke', ['class'=>'btn btn-xs btn-danger revoke-user','data-confirm-message'=>$revoke_message]) !!}
{!! Form::close() !!}
