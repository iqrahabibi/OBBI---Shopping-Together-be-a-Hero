{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
	<a href="{{ $detail_url }}" class="btn btn-xs btn-primary">Detail</a> |
  {!! Form::submit('Delete', ['class'=>'btn btn-xs btn-danger js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
