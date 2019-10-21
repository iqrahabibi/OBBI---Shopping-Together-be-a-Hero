<a href="{{ $referal_url }}" class="btn btn-xs btn-primary" target="_blank">List Referal</a>

{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
<a href="{{ $edit_url }}" class="btn btn-xs btn-warning">Ubah</a> |
{!! Form::submit('Delete', ['class'=>'btn btn-xs btn-danger js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
