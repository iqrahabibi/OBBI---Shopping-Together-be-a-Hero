<a href="{{$edit_url}}" class="btn btn-xs btn-success">Edit</a>
{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
  {!! Form::submit('Delete', ['class'=>'btn btn-xs btn-danger js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
<a href="{{ $status_url }}" class="btn btn-xs btn-warning">Ubah Status</a>