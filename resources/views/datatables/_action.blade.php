{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
	<a href="{{ $edit_url }}" class="btn btn-xs yellow">Ubah</a> |
  {!! Form::submit('Delete', ['class'=>'btn btn-xs red-intense js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
