{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
    <a href="{{ $license_owner_url }}" class="btn btn-xs blue">Owner Detail</a> |
    <a href="{{ $edit_url }}" class="btn btn-xs yellow">Ubah</a> |
    <a href="{{ $hibah_url }}" class="btn btn-xs purple">Hibah</a> |
  {!! Form::submit('Delete', ['class'=>'btn btn-xs red-intense js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
