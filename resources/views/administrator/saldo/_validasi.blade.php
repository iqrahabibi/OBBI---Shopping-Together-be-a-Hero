{!! Form::model($model,['url'=>$validasi_url,'method'=>'post','class'=>'form-inline']) !!}
  {!! Form::submit('Validasi', ['class'=>'btn btn-xs btn-primary js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
